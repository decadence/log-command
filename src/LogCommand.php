<?php

namespace Decadence;

use App;
use Carbon\Carbon;
use Decadence\Models\CronLog;
use Illuminate\Console\Command;

/**
 * Обёртка над командой для записи лога в базу данных
 */
class LogCommand extends Command
{
    /**
     * Сколько сообщений должен набрать лог перед сохранением в базу
     * @var int
     */
    protected $flushPer = 5;

    /**
     * Время запуска команды
     * @var int
     */
    protected $startTime = 0;

    /**
     * ID записи лога в базе
     * @var
     */
    protected $modelId;

    /**
     * Массив накопленных данных для лога
     * @var string
     */
    protected $logText = [];

    /**
     * Начало команды
     * @return void
     */
    protected function logStart()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Логирование вместе с сохранением в строку
     * @param $message
     */
    protected function log($message, $output = true)
    {
        $now = Carbon::now();

        $time = $now->format("H:i:s");

        if ($output) {
            $this->info($message);
        }

        $this->logText[] = "{$time} — {$message}";

        // если пора сбросить лог в БД
        if (count($this->logText) === $this->flushPer) {
            $this->flushLog();
        }
    }

    /**
     * Сброс текущего лога
     */
    protected function flushLog()
    {
        $this->createLog();

        $message = implode("\n", $this->logText);

        $item = CronLog::find($this->modelId);

        $item->output .= $message . "\n";
        $item->save();

        // сбрасываем текущий лог
        $this->logText = [];
    }

    /**
     * Задаёт лимит памяти
     * @param $mb int Мегабайты
     */
    protected function memoryLimit($mb)
    {
        ini_set("memory_limit", "{$mb}M");
    }

    /**
     * Создание записи лога
     */
    protected function createLog()
    {
        // запись уже есть
        if ($this->modelId) {
            return;
        }

        $item = new CronLog();

        $item->forceFill([
            "description" => $this->description,
            "command" => $this->getName(),
            "output" => "",
            "run_seconds" => 0,
        ]);

        $item->save();

        $this->modelId = $item->getKey();
    }

    /**
     * Окончание команды
     */
    protected function logFinish()
    {
        // сбрасываем остальные сообщения
        $this->flushLog();

        $runTime = round(microtime(true) - $this->startTime, 3);

        $item = CronLog::find($this->modelId);

        $item->forceFill([
            "run_seconds" => $runTime,
        ]);

        $item->save();
    }

    /**
     * Вывод информации о потреблении памяти
     */
    protected function memoryUsage()
    {
        $memory = round(memory_get_peak_usage() / 1024 / 1024, 2);
        $memoryNow = round(memory_get_usage() / 1024 / 1024, 2);

        $this->log("Потребление памяти (максимальное / текущее): {$memory} MB / {$memoryNow} MB");
    }
}

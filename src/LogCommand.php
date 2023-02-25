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
    protected int $flushPer = 10;

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
    protected array $logText = [
        "info" => [],
        "errors" => [],
    ];

    protected string $dateFormat = "H:i:s.u";

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
    protected function log($message, $output = true, $type = "info")
    {
        $now = Carbon::now();

        $time = $now->format($this->dateFormat);

        // выводить ли в текущую консоль
        if ($output) {
            $this->info($message);
        }

        $this->logText[$type][] = "{$time} — {$message}";

        // если пора сбросить лог в БД
        if ($this->getLogSize() === $this->flushPer) {
            $this->flushLog();
        }
    }

    /**
     * Суммарный размер лога по всем типам сообщений
     */ 
    protected function getLogSize() {
        $size = 0;

        foreach ($this->logText as $key => $value) {
            $size += count($value);
        }

        return $size;
    }

    /**
     * Сброс текущего лога в БД
     */
    protected function flushLog()
    {
        $this->createLog();

        $item = CronLog::find($this->modelId);

        if($this->logText["info"]) {
            $message = implode("\n", $this->logText["info"]);
            $item->output .= $message . "\n";
        }

        if($this->logText["errors"]) {
            $errors = implode("\n", $this->logText["errors"]);
            $item->errors .= $errors . "\n";
        }        

        $item->save();

        // сбрасываем текущий лог
        $this->logText = [
            "info" => [],
            "errors" => [],
        ];
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
            "errors" => "",
            "run_seconds" => 0,
        ]);

        $item->save();

        // запоминаем ID записи, с которой дальше работать
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
        $memoryMax = round(memory_get_peak_usage() / 1024 / 1024, 2);
        $memoryNow = round(memory_get_usage() / 1024 / 1024, 2);

        $this->log("Потребление памяти (максимальное / текущее): {$memoryMax} MB / {$memoryNow} MB");
    }
}

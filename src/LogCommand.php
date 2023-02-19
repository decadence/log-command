<?php

namespace App\Console;

use App;
use Carbon\Carbon;
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
    protected $id;

    /**
     * Путь к классу модели
     * @var
     */
    protected $modelClass;

    /**
     * Массив накопленных данных для лога
     * @var string
     */
    protected $logText = [];

    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Логирование вместе с сохранением в строку
     * @param $message
     */
    public function log($message, $output = true)
    {
        $now = Carbon::now();

        $time = $now->format("H:i:s");

        if ($output) {
            $this->info($message);
        }

        $this->logText[] = "{$time} — {$message}";

        // если пора сбросить лог в базу
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

        $item = $this->modelClass::find($this->id);

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
        if ($this->id) {
            return;
        }

        $item = new $this->modelClass();

        $item->forceFill([
            "description" => $this->description,
            "command" => $this->getName(),
            "output" => "",
            "run_seconds" => 0,
        ]);

        $item->save();

        $this->id = $item->getKey();
    }

    /**
     * Запоминаем время выполнения
     */
    public function record()
    {
        // сбрасываем остальные сообщения
        $this->flushLog();

        $runTime = round(microtime(true) - $this->startTime, 4);

        $item = $this->modelClass::find($this->id);

        $item->forceFill([
            "run_seconds" => $runTime,
        ]);

        $item->save();
    }

    /**
     * Вывод информации о потреблении памяти
     */
    protected function memory()
    {
        $memory = round(memory_get_peak_usage() / 1024 / 1024, 2);
        $memoryNow = round(memory_get_usage() / 1024 / 1024, 2);

        $this->log("Потребление памяти (max / current): {$memory} MB / {$memoryNow} MB");
    }
}

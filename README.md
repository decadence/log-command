# LogCommand
Класс для сохранения вывода Laravel-команды в БД

## Установка
`composer require decadence/log-command`

Скопировать миграцию и запустить её. Используемая модель `CronLog` включена в пакет. Если требуется, можно её наследовать или сделать свою модель, которая будет работать с таблицей cron_logs, если нужно добавлять туда методы.

```php
class CronLog extends \Decadence\Models\CronLog
{
    use Prunable;

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }

}
```

## Подключение
Команда должна наследовать класс `LogCommand`. В начале команды нужно вызвать метод `logStart`, в конце метод `logFinish`.
Для записи в лог БД нужно вызывать `log` вместо `info`.


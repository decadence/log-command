# LogCommand
Класс для сохранения вывода Laravel-команды в БД

## Установка
`composer require decadence/log-command`

## Подключение
Команда должна наследовать класс `LogCommand`. В начале команды нужно вызвать метод `logStart`, в конце метод `logFinish`.
Для записи в лог БД нужно вызывать `log` вместо `info`.


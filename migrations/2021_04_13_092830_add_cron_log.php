<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCronLog extends Migration
{
    public function up()
    {
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();

            $table->string("command", 1000)
                ->comment("Системное имя команды");
            
            $table->string("description", 1000)
                ->nullable()
                ->comment("Описание команды");

            $table->longText("output")
                ->nullable()
                ->comment("Вывод команды");

            $table->longText("errors")
                ->nullable()
                ->comment("Вывод команды (ошибки)");

            $table->decimal("run_seconds", 10, 3)
                ->comment("Время работы команды (секунды)");

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cron_logs');
    }
}

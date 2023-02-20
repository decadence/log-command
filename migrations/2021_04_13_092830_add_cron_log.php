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

            $table->string("command", 1000);
            
            $table->string("description", 1000)
                ->nullable();

            $table->longText("output");
            $table->decimal("run_seconds", 10, 3);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cron_logs');
    }
}

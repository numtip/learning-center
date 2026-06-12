<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mju_calendar_blocks', function (Blueprint $table) {
            $table->id();
            $table->date('blocked_date')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mju_calendar_blocks');
    }
};

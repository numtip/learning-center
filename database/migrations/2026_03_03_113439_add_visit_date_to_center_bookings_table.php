<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('center_bookings', function (Blueprint $table) {

            $table->date('visit_date')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('center_bookings', function (Blueprint $table) {
            $table->dropColumn('visit_date');
        });
    }
};

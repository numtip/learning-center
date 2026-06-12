<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('center_bookings', function (Blueprint $table) {
        // เพิ่มคอลัมน์ type และให้ค่าเริ่มต้นเป็น 'booking' เผื่อข้อมูลเก่าที่เคยมีอยู่แล้ว
        $table->string('type')->default('booking')->after('user_id');
    });
}

public function down()
{
    Schema::table('center_bookings', function (Blueprint $table) {
        $table->dropColumn('type');
    });
}
};

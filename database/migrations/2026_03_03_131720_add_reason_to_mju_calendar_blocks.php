<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('mju_calendar_blocks', function (Blueprint $table) {
            $table->string('reason')->default('full')->after('blocked_date');
        });
    }
    public function down() {
        Schema::table('mju_calendar_blocks', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};

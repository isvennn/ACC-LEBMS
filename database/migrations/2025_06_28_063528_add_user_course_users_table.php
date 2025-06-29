<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'course')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('course', ['BSIT', 'BSED', 'BEED', 'BSCRIM', 'BSHM', 'BSENTREP'])->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'course')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('course');
            });
        }
    }
};

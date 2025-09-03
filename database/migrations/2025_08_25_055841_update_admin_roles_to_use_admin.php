<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('admins')
            ->where('role', 'main_admin')
            ->update(['role' => 'admin']);
    }

    public function down()
    {
        DB::table('admins')
            ->where('role', 'admin')
            ->update(['role' => 'main_admin']);
    }
};
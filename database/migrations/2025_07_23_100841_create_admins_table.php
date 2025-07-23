<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
{
    Schema::create('admins', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique(); // ✅ ADD THIS LINE
        $table->string('password');
        $table->enum('role', ['main_admin', 'staff']);
        $table->timestamps();
    });
}


};

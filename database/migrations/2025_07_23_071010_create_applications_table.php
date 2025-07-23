<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
   Schema::create('applications', function (Blueprint $table) {
    $table->id();
    $table->string('email');
    $table->string('contact');
    $table->string('first_name');
    $table->string('middle_name')->nullable();
    $table->string('last_name');
    $table->string('birthdate');
    $table->integer('age');
    $table->boolean('is_pwd');
    $table->string('pwd_id')->nullable();
    $table->string('service_type'); // This will store "Tax Declaration", "Barangay Clearance", etc.
    $table->timestamps();
});

}

};

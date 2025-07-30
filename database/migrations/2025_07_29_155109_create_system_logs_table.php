<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type'); // e.g., 'daily_summary'
            $table->text('message');    // JSON or readable summary
            $table->json('data')->nullable(); // Store structured stats
            $table->timestamp('logged_at');   // When the event occurred
            $table->timestamps();      // When it was logged
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};
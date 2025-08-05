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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., ACCOUNT, QUEUE, SYSTEM
            $table->string('user')->nullable(); // name of admin/user who triggered action
            $table->text('action'); // description of the action
            $table->json('details')->nullable(); // extra data like field changes
            $table->string('status'); // SUCCESS, FAILED, PENDING
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
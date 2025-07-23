<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('service_type');
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_senior')->default(false);
            $table->enum('status', ['pending', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('queue_entries');
    }
};

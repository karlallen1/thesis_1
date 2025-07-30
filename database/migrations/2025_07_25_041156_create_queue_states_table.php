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
    public function up(): void
    {
        Schema::create('queue_states', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        // Insert default queue states
        DB::table('queue_states')->insert([
            [
                'key' => 'now_serving',
                'value' => 'null',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'regular_served',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'cancelled_count',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_states');
    }
};
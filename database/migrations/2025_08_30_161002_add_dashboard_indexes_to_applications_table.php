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
        Schema::table('applications', function (Blueprint $table) {
            // For clients_served query: entered_queue = true AND DATE(queue_entered_at) = ?
            $table->index(['entered_queue', 'queue_entered_at'], 'idx_entered_queue_date');
            
            // For pending query: is_preapplied = true AND entered_queue = false AND status = 'pending'
            $table->index(['is_preapplied', 'entered_queue', 'status'], 'idx_preapplied_status');
            
            // For PWD clients: is_pwd = true AND entered_queue = true AND DATE(queue_entered_at) = ?
            $table->index(['is_pwd', 'entered_queue', 'queue_entered_at'], 'idx_pwd_queue');
            
            // For senior clients by age: entered_queue = true AND age >= 60
            $table->index(['entered_queue', 'age', 'queue_entered_at'], 'idx_senior_queue');
            
            // For senior clients by senior_id: entered_queue = true AND senior_id IS NOT NULL
            $table->index(['entered_queue', 'senior_id', 'queue_entered_at'], 'idx_senior_id_queue');
            
            // General status index
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_entered_queue_date');
            $table->dropIndex('idx_preapplied_status');
            $table->dropIndex('idx_pwd_queue');
            $table->dropIndex('idx_senior_queue');
            $table->dropIndex('idx_senior_id_queue');
            $table->dropIndex('idx_status');
        });
    }
};
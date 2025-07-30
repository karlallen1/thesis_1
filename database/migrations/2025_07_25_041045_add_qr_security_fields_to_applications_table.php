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
            // Add the new QR security fields
            $table->string('qr_token', 64)->nullable()->after('service_type');
            $table->timestamp('qr_expires_at')->nullable()->after('qr_token');
            $table->timestamp('queue_entered_at')->nullable()->after('qr_expires_at');
            
            // Add index for faster QR token lookups
            $table->index(['qr_token', 'qr_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Remove the index first
            $table->dropIndex(['qr_token', 'qr_expires_at']);
            
            // Then remove the columns
            $table->dropColumn(['qr_token', 'qr_expires_at', 'queue_entered_at']);
        });
    }
};
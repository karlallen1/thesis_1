<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mailbox_submissions', function (Blueprint $table) {
            // Add only if they don't exist
            if (!Schema::hasColumn('mailbox_submissions', 'mailbox_used_at')) {
                $table->timestamp('mailbox_used_at')->nullable()
                      ->after('pin_expires_at')
                      ->comment('When PIN was used at IoT device');
            }

            if (!Schema::hasColumn('mailbox_submissions', 'admin_status')) {
                $table->enum('admin_status', ['pending', 'approved', 'disapproved'])
                      ->default('pending')
                      ->after('mailbox_used_at');
            }
        });

        // Optional: Add indexes if not present
        Schema::table('mailbox_submissions', function (Blueprint $table) {
            if (!Schema::hasIndex('mailbox_submissions', 'idx_mailbox_used_at')) {
                $table->index('mailbox_used_at', 'idx_mailbox_used_at');
            }
            if (!Schema::hasIndex('mailbox_submissions', 'idx_admin_status')) {
                $table->index('admin_status', 'idx_admin_status');
            }
        });
    }

    public function down()
    {
        Schema::table('mailbox_submissions', function (Blueprint $table) {
            $table->dropColumn(['mailbox_used_at', 'admin_status']);
            $table->dropIndex('idx_mailbox_used_at');
            $table->dropIndex('idx_admin_status');
        });
    }
};
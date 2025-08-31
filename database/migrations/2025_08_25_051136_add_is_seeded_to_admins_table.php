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
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_seeded')->default(false)->after('role');
        });

        // Update existing main_admin to super_admin and mark as seeded
        // This assumes you want to convert the first main_admin to super_admin
        $firstMainAdmin = DB::table('admins')
            ->where('role', 'main_admin')
            ->orderBy('id')
            ->first();

        if ($firstMainAdmin) {
            DB::table('admins')
                ->where('id', $firstMainAdmin->id)
                ->update([
                    'role' => 'super_admin',
                    'is_seeded' => true
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert super_admin back to main_admin
        DB::table('admins')
            ->where('role', 'super_admin')
            ->update(['role' => 'main_admin']);

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_seeded');
        });
    }
};
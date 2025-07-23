<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQueueNumberToApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('queue_number')->nullable()->after('service_type');
        });
    }

    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('queue_number');
        });
    }
}

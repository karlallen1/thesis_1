<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mailbox_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('email');
            $table->string('contact', 20);
            $table->date('birthdate');
            $table->integer('age');
            $table->boolean('is_pwd')->default(false);
            $table->string('pwd_id', 50)->nullable();
            $table->string('senior_id', 50)->nullable();
            $table->string('service_type');
            $table->string('pin_code', 6)->unique();
            $table->timestamp('pin_expires_at');
            $table->boolean('documents_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->string('mailbox_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mailbox_submissions');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('class_name');
            $table->timestamp('last_check_at')->nullable();
            $table->timestamp('last_succeed_at')->nullable();
            $table->timestamp('last_fail_at')->nullable();
            $table->text('last_fail_reason')->nullable();
            $table->integer('fail_count')->nullable();
            $table->timestamp('next_check_after')->nullable();
            $table->string('source_url');
            $table->string('name');
            $table->string('base_url')->nullable();
            $table->string('home_url')->nullable();
            $table->string('frequency')->nullable();
            $table->boolean('respect_timestamp')->default(true);
            $table->boolean('active')->default(true);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sources');
    }

};

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
        Schema::create('message', function (Blueprint $table) {
            $table->id();
//            $table->increments('message_id');
            $table->integer('message_id')->nullable();
            $table->string('subject',500);
            $table->text('body');
            $table->integer('to');
            $table->integer('from');
            $table->dateTime('datetime');
            $table->tinyInteger('isRead');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message');
    }
};

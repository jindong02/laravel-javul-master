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
        Schema::create('user_funds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('user_from_id')->unsigned();
            $table->foreign('user_from_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_to_id')->unsigned();
            $table->foreign('user_to_id')->references('id')->on('users');

            $table->decimal('amount',10,2);
            $table->string('type',20)->comment("donated or rewarded");

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_funds');
    }
};

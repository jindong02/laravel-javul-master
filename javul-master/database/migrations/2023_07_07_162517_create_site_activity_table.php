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
        Schema::create('site_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('objective_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();

            $table->unsignedBigInteger('issue_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->text('comment');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_activity');
    }
};

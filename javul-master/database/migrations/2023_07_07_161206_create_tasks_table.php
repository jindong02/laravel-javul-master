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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units');

            $table->unsignedBigInteger('objective_id')->unsigned()->nullable();
            $table->foreign('objective_id')->references('id')->on('objectives');

            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->string('comment');
            $table->text('task_action');
            $table->text('summary')->nullable();
            $table->string('skills');
            $table->dateTime('estimated_completion_time_start')->nullable();
            $table->dateTime('estimated_completion_time_end')->nullable();
            $table->decimal('compensation',10,2)->nullable();

            $table->unsignedBigInteger('assign_to')->nullable();
            $table->foreign('assign_to')->references('id')->on('users');

            $table->string('status');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

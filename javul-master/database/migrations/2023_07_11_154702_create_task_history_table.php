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
        Schema::create('task_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units');

            $table->unsignedBigInteger('objective_id')->nullable();
            $table->foreign('objective_id')->references('id')->on('objectives');

            $table->string('name');
            $table->text('description');
            $table->text('task_action');
            $table->text('task_documents');
            $table->text('summary')->nullable();
            $table->string('skills');
            $table->dateTime('estimated_completion_time_start');
            $table->dateTime('estimated_completion_time_end');
            $table->decimal('compensation',10,2);
            $table->text('updatedFields')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_history');
    }
};

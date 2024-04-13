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
        Schema::create('tasks_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('objective_id');
            $table->string('name',255);
            $table->text('description');
            $table->string('size',10);
            $table->string('comment',255);
            $table->text('task_action');
            $table->text('summary');
            $table->string('skills',255);
            $table->dateTime('estimated_completion_time_start');
            $table->dateTime('estimated_completion_time_end');
            $table->decimal('compensation',10,2);
            $table->unsignedBigInteger('assign_to');
            $table->string('status',255);
            $table->unsignedBigInteger('modified_by');
            $table->dateTime('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks_revisions');
    }
};

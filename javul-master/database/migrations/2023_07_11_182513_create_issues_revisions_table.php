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
        Schema::create('issues_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('objective_id');
            $table->unsignedBigInteger('task_id');
            $table->string('title',255);
            $table->text('description');
            $table->text('resolution');
            $table->text('file_attachments');
            $table->string('status',255);
            $table->unsignedBigInteger('verified_by');
            $table->unsignedBigInteger('resolved_by');
            $table->dateTime('deleted_at');
            $table->unsignedBigInteger('modified_by');
            $table->integer('size');
            $table->integer('issues_id');
            $table->string('comment',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues_revisions');
    }
};

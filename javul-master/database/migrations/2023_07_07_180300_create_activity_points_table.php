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
        Schema::create('activity_points', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('objective_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->integer('issue_id')->nullable();
            $table->integer('points');
            $table->text('comments');
            $table->string('type')->comment('unit,objective,task or issue');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_points');
    }
};

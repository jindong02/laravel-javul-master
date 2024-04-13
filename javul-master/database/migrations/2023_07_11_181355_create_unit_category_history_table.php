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
        Schema::create('unit_category_history', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id')->nullable();
            $table->integer('unit_category_id')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('parent_id_belongs_to',20)->nullable();
            $table->text('category_hierarchy')->nullable();
            $table->string('action_type')->comment('added,deleted or updated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_category_history');
    }
};

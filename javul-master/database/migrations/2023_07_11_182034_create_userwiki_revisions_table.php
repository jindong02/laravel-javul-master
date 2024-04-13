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
        Schema::create('userwiki_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('user_id');
            $table->string('page_title',255);
            $table->text('page_content');
            $table->string('comment',255);
            $table->string('slug',30);
            $table->tinyInteger('private');
            $table->unsignedBigInteger('size');
            $table->unsignedBigInteger('modify_by');
            $table->unsignedBigInteger('page_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userwiki_revisions');
    }
};

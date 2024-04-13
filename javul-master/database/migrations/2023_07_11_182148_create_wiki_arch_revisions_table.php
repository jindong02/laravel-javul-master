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
        Schema::create('wiki_arch_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('revision_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('wiki_page_id');
            $table->text('rev_page_content');
            $table->string('change_byte',255);
            $table->string('edit_comment',255);
            $table->unsignedBigInteger('user_id');
            $table->dateTime('time_stamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wiki_arch_revisions');
    }
};

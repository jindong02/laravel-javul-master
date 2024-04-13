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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->tinyInteger('all')->default(0);
            $table->tinyInteger('account_creation')->default(1);
            $table->tinyInteger('confirmation_email')->default(1);
            $table->tinyInteger('forum_replies')->default(0);
            $table->tinyInteger('watched_items')->default(0);
            $table->tinyInteger('inbox')->default(0);
            $table->tinyInteger('fund_received')->default(0);
            $table->tinyInteger('task_management')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};

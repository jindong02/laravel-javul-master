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
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('unit_id')->nullable();
            $table->integer('objective_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->decimal('amount',10,2);
            $table->string('transaction_type')->comment("donated or rewarded");
            $table->string('payment_id');
            $table->string('status');
            $table->string('fund_type')->comment("units or objectives or tasks or general purpose");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};

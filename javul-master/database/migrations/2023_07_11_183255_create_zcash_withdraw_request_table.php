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
        Schema::create('zcash_withdraw_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index('coinbase_transaction_user_id_foreign');
            $table->integer('user_transaction_id');
            $table->text('transfer_transaction_id')->nullable();
            $table->text('amount');
            $table->text('zcash_address');
            $table->string('status', 100)->comment('withdrawal,rejected,approved');
            $table->text('transaction_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zcash_withdraw_request');
    }
};

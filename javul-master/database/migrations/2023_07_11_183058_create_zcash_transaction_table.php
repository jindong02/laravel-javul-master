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
        Schema::create('zcash_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fund_id')->nullable()->index('coinbase_transaction_fund_id_foreign');
            $table->integer('user_transaction_id')->nullable();
            $table->text('transaction_id')->nullable()->comment('zcash transaction id');
            $table->text('amount')->nullable();
            $table->text('zcash_address')->nullable();
            $table->string('status')->nullable()->comment('success,pending,cancel');
            $table->text('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zcash_transaction');
    }
};

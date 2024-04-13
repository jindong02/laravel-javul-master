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
        Schema::create('zcash_webhook_data', function (Blueprint $table) {
            $table->id();
            $table->text('transaction_id');
            $table->text('zcash_address')->nullable();
            $table->string('notification_status', 100);
            $table->text('notification_data')->nullable();
            $table->text('transaction_data')->nullable()->comment('it comes from wallet transfer details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zcash_webhook_data');
    }
};

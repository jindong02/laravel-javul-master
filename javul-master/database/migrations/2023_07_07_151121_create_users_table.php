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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->string('address')->nullable();

            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            $table->unsignedBigInteger('country_id')->nullable();

            $table->unsignedBigInteger('state_id')->nullable();

            $table->unsignedBigInteger('city_id')->nullable();

            $table->string('job_skills')->nullable()->comment("reference to job_skill table. multiple with comma.");
            $table->string('area_of_interest')->nullable()->comment("reference to area_of_interest table. multiple with comma.");
            $table->string('role')->nullable();
            $table->string('profile_pic')->nullable();
            $table->bigInteger('loggedin')->nullable();
            $table->string('paypal_email')->nullable();
            $table->integer('activity_points')->nullable();
            $table->string('email_token')->nullable();
            $table->integer('is_email_verified')->default(0);
            $table->string('timezone')->nullable();
            $table->decimal('quality_of_work',8,2)->nullable();
            $table->decimal('timeliness',8,2)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

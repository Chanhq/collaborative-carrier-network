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
        Schema::create('auction_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transport_request_id');
            $table->float('revenue_gain')->default(0);
            $table->timestamps();

            $table->foreign('auction_id')->references('id')->on('auctions');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('transport_request_id')->references('id')->on('transport_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_evaluations');
    }
};

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
        Schema::create('bid_evaluation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bid_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transport_request_id');
            $table->float('revenue_gain');
            $table->timestamps();

            $table->foreign('bid_id')->references('id')->on('auction_bids');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('transport_request_id')->references('id')->on('transport_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_evaluation');
    }
};

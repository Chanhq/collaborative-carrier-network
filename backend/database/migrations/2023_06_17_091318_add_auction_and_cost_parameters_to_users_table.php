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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('transport_request_minimum_revenue')->default(70);
            $table->integer('transport_request_cost_base')->default(10);
            $table->integer('transport_request_cost_variable')->default(1);
            $table->integer('transport_request_price_base')->default(20);
            $table->integer('transport_request_price_variable')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('transport_request_minimum_revenue');
            $table->dropColumn('transport_request_cost_base');
            $table->dropColumn('transport_request_cost_variable');
            $table->dropColumn('transport_request_price_base');
            $table->dropColumn('transport_request_price_variable');
        });
    }
};

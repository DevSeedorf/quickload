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
        Schema::create('reserved_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('contract_code');
            $table->string('account_reference');
            $table->string('account_name');
            $table->string('currency_code');
            $table->string('customer_email');
            $table->string('customer_name');
            $table->string('bank_code');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('reservation_reference');
            $table->string('reserved_account_type');
            $table->string('status');
            $table->timestamps();
        
            // Add foreign key constraint if applicable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_accounts');
    }
};

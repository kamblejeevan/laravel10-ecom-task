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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('payment_date');
            $table->string('payment_intend')->nullable();
            $table->string('customer_email');
            $table->float('amount');
            $table->timestamp('intiated_at')->nullable();
            $table->timestamp('payment_at')->nullable();
            $table->tinyInteger('status')->comment("1->Completed, 2->failed");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

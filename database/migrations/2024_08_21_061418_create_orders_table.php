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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('customer_id'); 
            $table->decimal('total_amount', 8, 2); 
            $table->boolean('status')->default(false); 
            $table->timestamps(); 
            
            // Foreign Key Constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Indexing
            $table->index('customer_id'); // Index on customer_id for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

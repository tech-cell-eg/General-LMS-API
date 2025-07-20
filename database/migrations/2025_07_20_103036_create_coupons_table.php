<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('offer_name');
            $table->string('code')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['draft', 'active', 'expired', 'scheduled'])->default('draft');
            $table->integer('quantity')->nullable();
            $table->integer('redemptions')->default(0);
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->integer('uses_per_customer')->default(1);
            $table->integer('priority')->default(0);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

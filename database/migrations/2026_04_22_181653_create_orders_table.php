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
            $table->string('order_number')->unique();
            $table
                ->foreignId('package_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('software_key_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_ip', 45)->nullable()->index();
            $table->unsignedInteger('original_amount');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('VND');
            $table->string('coupon_code')->nullable();
            $table->string('payment_provider')->default('sepay');
            $table->string('payment_gateway')->nullable();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->string('payment_status')->default('pending')->index();
            $table->string('fulfillment_status')->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
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

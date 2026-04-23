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
        Schema::create('software_keys', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('package_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->string('label')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('license_key')->nullable();
            $table->text('notes')->nullable();
            $table->json('extra_data')->nullable();
            $table->string('status')->default('available')->index();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_keys');
    }
};

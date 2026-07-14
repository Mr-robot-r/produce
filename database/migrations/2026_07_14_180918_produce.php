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

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });


        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('base_price', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->timestamp('date');
            $table->enum('type', ['inbound', 'outbound']);
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->string('counterparty')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'canceled'])->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('warehouse_id');
        });

        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 18, 2)->unsigned();
            $table->timestamps();

            $table->index(['voucher_id', 'product_id']);
        });
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('child_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 18, 2)->unsigned();
            $table->timestamps();

            $table->unique(['parent_product_id', 'child_product_id']);
            $table->index('parent_product_id');
            $table->index('child_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_items');
        Schema::dropIfExists('boms');
    }
};

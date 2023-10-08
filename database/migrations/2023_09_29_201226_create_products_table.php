<?php

use App\Models\User;
use Database\Seeders\ProductSeeder;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('product_type');
            $table->string('sku')->index();
            $table->integer('current_stock')->nullable();
            $table->integer('price')->default(100);
            $table->integer('created_by')->nullable(); //null for now
            $table->integer('updated_by')->nullable();
            $table->integer('stock_notification_limit')->nullable();
            $table->dateTime('stock_notification_started_at')->nullable();
            $table->dateTime('stock_notification_sent_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

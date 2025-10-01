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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('category')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('price')->nullable();
            $table->string('discount_price')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->string('amazon')->nullable();
            $table->string('e_bay')->nullable();
            $table->string('etsy')->nullable();
            $table->string('walmart')->nullable();
            $table->string('description')->nullable();
             $table->integer('status')->default(1);
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

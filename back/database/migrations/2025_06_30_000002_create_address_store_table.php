<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_id')->constrained('addresses')->onDelete('cascade')->onDelete('restrict');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_store');
    }
};
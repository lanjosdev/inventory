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
        Schema::create('brand_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_brand')->constrained('brands')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('fk_contact')->constrained('contacts')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_contacts');
    }
};
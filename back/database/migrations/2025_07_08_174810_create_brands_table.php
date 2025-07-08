<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('observation', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('brands')->insert([
            ['name' => 'Coca-Cola', 'observation' => 'Multinacional de bebidas'],
            ['name' => 'Nike', 'observation' => 'Empresa de artigos esportivos'],
            ['name' => 'McDonald\'s', 'observation' => 'Rede de fast-food'],
            ['name' => 'Apple', 'observation' => 'Empresa de tecnologia'],
            ['name' => 'Samsung', 'observation' => 'Conglomerado sul-coreano']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
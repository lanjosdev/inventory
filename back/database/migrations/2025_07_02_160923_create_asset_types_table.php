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
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('observation', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('asset_types')->insert([
            ['name' => 'Mídia Externa e de Grande Formato', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mídia em Pontos de Acesso e Circulação', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mídia no Ponto de Venda (PDV)', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mídia Digital (Digital Out-of-Home)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_types');
    }
};
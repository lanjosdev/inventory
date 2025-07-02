<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        // Inserção dos setores
        DB::table('sectors')->insert([
            ['name' => 'Bazar', 'description' => 'Produtos diversos para casa, decoração e utilidades domésticas.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Eletro', 'description' => 'Eletrodomésticos e eletrônicos.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mercearia seca', 'description' => 'Alimentos não perecíveis como grãos, enlatados e farináceos.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mercearia líquida', 'description' => 'Bebidas em geral', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Higiene e beleza', 'description' => 'Produtos de cuidados pessoais e cosméticos.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Limpeza', 'description' => 'Produtos para limpeza em geral.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FLV', 'description' => 'Frutas, legumes e verduras', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Açougue', 'description' => 'Carnes de peso variável', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Padaria', 'description' => 'Pães, bolos, salgados e produtos de confeitaria frescos', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lanchonete', 'description' => 'Lanches rápidos, bebidas e refeições leves prontas para consumo.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Checkout', 'description' => 'Área de caixas para pagamento e finalização de compras.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Congelados', 'description' => 'Alimentos congelados como pizzas e pratos prontos.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Refrigerados', 'description' => 'Laticínios, frios e produtos que necessitam de refrigeração.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Estacionamento', 'description' => 'Área para estacionamento de veículos dos clientes.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fachada', 'description' => 'Parte externa do estabelecimento, com vitrines e identificação visual.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Galeria', 'description' => 'Espaço com lojas adicionais, fora do checkout.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectors');
    }
};
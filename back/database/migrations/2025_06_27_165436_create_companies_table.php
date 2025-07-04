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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        // Inserção dos registros completos
        DB::table('companies')->insert([
            [
                'name' => 'Savegnago',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paulistão Atacadista',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
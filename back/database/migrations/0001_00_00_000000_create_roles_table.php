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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('roles')->insert([
            ['name' => "Admin", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Manager", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Companie", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Store", 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
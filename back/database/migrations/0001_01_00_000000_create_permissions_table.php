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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('permissions')->insert([
            ['name' => "C", 'description' => "create", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "R", 'description' => "read", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "U", 'namedescription' => "update", 'created_at' => now(), 'updated_at' => now()],
            ['name' => "D", 'namedescription' => "delete", 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
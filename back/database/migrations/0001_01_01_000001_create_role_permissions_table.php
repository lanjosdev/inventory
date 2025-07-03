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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_role')->constrained('roles')->onUpdate('cascade');
            $table->foreignId('fk_permission')->constrained('permissions')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('role_permissions')->insert([
            ['fk_role' => 1,'fk_permission' => 1],
            ['fk_role' => 1,'fk_permission' => 2],
            ['fk_role' => 1,'fk_permission' => 3],
            ['fk_role' => 1,'fk_permission' => 4],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
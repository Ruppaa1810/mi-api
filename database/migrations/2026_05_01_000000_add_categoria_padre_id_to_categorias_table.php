<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->foreignId('categoria_padre_id')
                  ->nullable()
                  ->constrained('categorias')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropForeign(['categoria_padre_id']);
            $table->dropColumn('categoria_padre_id');
        });
    }
};

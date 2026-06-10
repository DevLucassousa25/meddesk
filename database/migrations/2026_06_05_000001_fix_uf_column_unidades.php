<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona uf se não existir (seguro para reexecutar)
        if (!Schema::hasColumn('unidades', 'uf')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->string('uf', 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('unidades', 'uf')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->dropColumn('uf');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            // 'principal' = sede principal | 'filial' = unidade filial
            $table->string('tipo')->default('filial')->after('nome');
        });

        // A primeira unidade cadastrada é a principal
        DB::table('unidades')->orderBy('id')->limit(1)->update(['tipo' => 'principal']);
    }

    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};

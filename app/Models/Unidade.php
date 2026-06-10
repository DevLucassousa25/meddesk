<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $fillable = ['nome', 'tipo', 'cidade', 'uf', 'endereco', 'ativo'];

    public function isPrincipal(): bool { return $this->tipo === 'principal'; }
    public function isFilial(): bool    { return $this->tipo === 'filial'; }

    public function scopePrincipal($query) { return $query->where('tipo', 'principal'); }

    protected $casts = ['ativo' => 'boolean'];

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfissionalUnidade extends Model
{
    protected $table = 'profissional_unidade';

    protected $fillable = ['profissional_id', 'unidade', 'dias'];

    protected $casts = ['dias' => 'array'];

    public function profissional(): BelongsTo
    {
        return $this->belongsTo(Profissional::class);
    }
}

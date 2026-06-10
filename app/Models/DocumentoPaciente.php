<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoPaciente extends Model
{
    use SoftDeletes;

    protected $table = 'documentos_paciente';

    protected $fillable = [
        'paciente_id','nome','tipo','arquivo','mime_type','tamanho','descricao','data_documento',
    ];

    protected $casts = [
        'data_documento' => 'date',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }

    public static function tipos(): array
    {
        return ['exame'=>'Exame','laudo'=>'Laudo','receita'=>'Receita','guia'=>'Guia','contrato'=>'Contrato','outros'=>'Outros'];
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        if (!$this->tamanho) return '—';
        $kb = $this->tamanho / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }
}

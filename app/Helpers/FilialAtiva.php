<?php

namespace App\Helpers;

use App\Models\Unidade;
use Illuminate\Support\Facades\Session;

class FilialAtiva
{
    /** Retorna a Unidade ativa na sessão, ou null (= todas as filiais). */
    public static function get(): ?Unidade
    {
        $id = Session::get('unidade_ativa_id');
        if (! $id) return null;

        return Unidade::ativas()->find($id);
    }

    /** Retorna apenas o ID, ou null. */
    public static function id(): ?int
    {
        return Session::get('unidade_ativa_id');
    }

    /** Retorna true se uma filial específica está selecionada. */
    public static function selecionada(): bool
    {
        return (bool) Session::get('unidade_ativa_id');
    }
}

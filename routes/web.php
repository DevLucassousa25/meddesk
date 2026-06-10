<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Clientes\CadastroCliente;
use App\Livewire\Clientes\DetalhesPaciente;
use App\Livewire\Clientes\ListaPacientes;
use App\Livewire\Profissionais\CadastroProfissional;
use App\Livewire\Profissionais\DetalhesProfissional;
use App\Livewire\Profissionais\ListaProfissionais;
use App\Livewire\Configuracoes\Especialidades;
use App\Livewire\Configuracoes\TabelaPrecos;
use App\Livewire\Configuracoes\Unidades;
use App\Livewire\Configuracoes\Salas;
use App\Livewire\Financeiro\Pacotes;

Route::get('/', fn() => redirect()->route('pacientes.lista'));

Route::get('/pacientes',                      ListaPacientes::class)->name('pacientes.lista');
Route::get('/pacientes/{id}',                 DetalhesPaciente::class)->name('pacientes.detalhes');
Route::get('/paciente/cadastro',              CadastroCliente::class)->name('pacientes.cadastro');
Route::get('/paciente/{pacienteId}/editar',   CadastroCliente::class)->name('pacientes.editar');

// Profissionais
Route::get('/profissionais',                           ListaProfissionais::class)->name('profissionais.lista');
Route::get('/profissional/cadastro',                   CadastroProfissional::class)->name('profissionais.cadastro');
Route::get('/profissional/{profissionalId}/editar',    CadastroProfissional::class)->name('profissionais.editar');
Route::get('/profissional/{profissionalId}/detalhes',  DetalhesProfissional::class)->name('profissionais.detalhes');

// Filial ativa
Route::post('/filial/trocar', function (\Illuminate\Http\Request $request) {
    $id = $request->input('unidade_id');
    if ($id === 'todas') {
        session()->forget('unidade_ativa_id');
    } else {
        $unidade = \App\Models\Unidade::ativas()->findOrFail($id);
        session(['unidade_ativa_id' => $unidade->id]);
    }
    return back();
})->name('filial.trocar');

// Configurações
Route::get('/configuracoes/especialidades',  Especialidades::class)->name('config.especialidades');
Route::get('/configuracoes/unidades',        Unidades::class)->name('config.unidades');
Route::get('/configuracoes/salas',           Salas::class)->name('config.salas');
Route::get('/configuracoes/tabela-precos',   TabelaPrecos::class)->name('config.tabela-precos');

// Financeiro
Route::get('/financeiro/pacotes', Pacotes::class)->name('financeiro.pacotes');

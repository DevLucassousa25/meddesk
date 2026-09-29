# MedDesk — Sistema de Gestão para Clínicas

> 🚧 **Projeto em desenvolvimento.** Algumas funcionalidades já estão prontas e outras ainda estão em construção. Veja abaixo o que já foi implementado e o que vem a seguir.

Sistema web para gestão de clínicas com uma ou mais unidades, focado em cadastro de pacientes e profissionais, estrutura física, tabela de preços e pacotes de atendimento. O projeto já inclui dados de exemplo para uma clínica multidisciplinar de atendimento ao TEA (Transtorno do Espectro Autista).

Desenvolvido com **Laravel 13**, **Livewire 3** e **PostgreSQL**.

---

## Funcionalidades

### ✅ Já implementado

**Pacientes**
- Listagem, cadastro e edição de pacientes com dados pessoais, contato e endereço
- Ficha do paciente organizada em abas: dados gerais, agenda, atendimentos, prontuário, anotações, documentos, financeiro e linha do tempo

**Profissionais**
- Cadastro completo de profissionais, com especialidades e tipo de vínculo
- Definição dos dias de atendimento em cada unidade
- Página de detalhes do profissional

**Multiunidade**
- Cadastro de unidades (matriz e filiais)
- Seletor de filial ativa: o usuário alterna a unidade e os dados são filtrados automaticamente

**Configurações**
- Especialidades vinculadas a uma ou mais unidades
- Salas por unidade, com tipo, capacidade e cor de identificação
- Tabela de preços por especialidade, com duração do atendimento e valor por unidade

**Financeiro**
- Pacotes de atendimento com valor bruto, desconto (percentual ou fixo), valor final e validade

### 🔜 Em desenvolvimento

- Agendamento de consultas e terapias
- Registro de atendimentos e prontuário eletrônico
- Upload e gestão de documentos do paciente
- Cobranças e integração com convênios
- Autenticação e perfis de acesso
- Testes automatizados
- Ambiente em Docker

---

## Tecnologias

| Camada | Tecnologias |
|---|---|
| Back-end | PHP 8.3, Laravel 13, Livewire 3 |
| Front-end | Blade, Tailwind CSS 4, Vite, Lucide Icons |
| Banco de dados | PostgreSQL |
| Qualidade | Pest, Laravel Pint |
| Idioma | Interface e validações em português (pt_BR) |

---

## Como rodar

**Pré-requisitos:** PHP 8.3+, Composer, Node.js e PostgreSQL.

```bash
# 1. Clonar o repositório
git clone https://github.com/DevLucassousa25/meddesk.git
cd meddesk

# 2. Instalar dependências
composer install
npm install

# 3. Configurar o ambiente
cp .env.example .env
php artisan key:generate
# Ajuste DB_DATABASE, DB_USERNAME e DB_PASSWORD no .env

# 4. Criar as tabelas e popular com dados de exemplo
php artisan migrate --seed

# 5. Subir a aplicação
composer run dev
```

Acesse em **http://localhost:8000**.

---

## Autor

**Lucas Sousa** — Desenvolvedor Full Stack
[LinkedIn](https://www.linkedin.com/in/lucas-sousa-a10474212/) · [GitHub](https://github.com/DevLucassousa25)

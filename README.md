# 🔧 Sistema de Oficina Mecânica — Lab 03

Projeto desenvolvido para a disciplina de **Banco de Dados** do curso de 
Ciência da Computação / Engenharia de Software — IDP, 2026/1.

## 📋 Sobre o sistema

Sistema de gerenciamento de uma oficina mecânica, contemplando o controle 
de clientes, veículos, mecânicos, ordens de serviço, peças e serviços, 
com módulo de autenticação e gerenciamento de usuários.

## 🗄️ Estrutura do banco de dados

O banco `oficina` é composto pelas seguintes tabelas:

- `enderecos` — dados de endereço
- `clientes` — cadastro de clientes com documento e endereço
- `contato` — telefones/contatos dos clientes
- `marcas` / `modelos` — catálogo de veículos
- `veiculos` — veículos vinculados aos clientes
- `mecanicos` — equipe de mecânicos e especialidades
- `ordens_servicos` — OS com status, datas e garantia
- `pecas` — estoque de peças com preço
- `itens_os_pecas` — peças utilizadas por OS
- `servicos_catalogos` — catálogo de serviços
- `itens_os_servicos` — serviços executados por OS
- `usuarios` — autenticação e perfis de acesso *(Lab 03)*
- `perfis` — tabela de perfis *(Lab 03)*

## 👥 Perfis de acesso

| Perfil | Permissões |
|---|---|
| Administrador | Acesso total ao sistema |
| Gerência | Visualização e edição operacional |
| Usuário Comum | Acesso apenas às próprias informações |

## ⚙️ Funcionalidades (Lab 03)

- [x] CRUD de usuários
- [x] Login com autenticação
- [x] Hash de senhas
- [x] Controle de perfis e permissões
- [x] Prepared Statements (prevenção de SQL Injection)

## 🏗️ Estrutura do projeto (MVC)
/
├── models/       # Acesso ao banco de dados
├── views/        # Interface do sistema
├── controllers/  # Regras de negócio
└── SQL_grupo05.sql

## 📅 Entrega

Prazo: **25/05/2026 às 09h59**
Disciplina: Banco de Dados — Prof. Moises Silva de Sousa

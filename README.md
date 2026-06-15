# 🔧 Sistema de Oficina Mecânica — Lab 04

Projeto desenvolvido para a disciplina de **Banco de Dados** do curso de
Ciência da Computação / Engenharia de Software — IDP, 2026/1.

---

## 📋 Sobre o sistema

Sistema web de gerenciamento de uma oficina mecânica, contemplando controle
de clientes, veículos, mecânicos, ordens de serviço, peças e serviços,
com módulo completo de autenticação e controle de acesso por perfil.

---

## 👥 Integrantes

| Nome   |
|--------|
| Bianca | `
| Yuri   | 
| Luiz   |  
| Elane  | 

---

## 🗄️ Diagrama do Banco de Dados

![Diagrama](docs/Diagrama_grupo05.png)

---

## 🗄️ Estrutura do banco de dados

O banco `oficina` é composto pelas seguintes tabelas:

- `enderecos` — dados de endereço dos clientes
- `clientes` — cadastro de clientes com documento, perfil e senha
- `contato` — telefones/contatos dos clientes
- `marcas` / `modelos` — catálogo de marcas e modelos de veículos
- `veiculos` — veículos vinculados aos clientes
- `mecanicos` — equipe de mecânicos com especialidade e disponibilidade
- `ordens_servicos` — OS com status, datas e garantia
- `pecas` — estoque de peças com preço unitário
- `itens_os_pecas` — peças utilizadas por OS
- `servicos_catalagos` — catálogo de serviços disponíveis
- `itens_os_servicos` — serviços executados por OS

---

## 👥 Perfis de acesso

| Perfil | Permissões |
|---|---|
| **Administrador** | Acesso total — CRUD completo em todos os módulos |
| **Gerência** | Visualização geral e criação de ordens de serviço |
| **Usuário Comum** | Acesso restrito — apenas visualização |

---

## ⚙️ Funcionalidades

- [x] Login com autenticação por documento e senha
- [x] Hash de senhas com `password_hash` (bcrypt)
- [x] Controle de perfis e permissões por rota
- [x] Prepared Statements (prevenção de SQL Injection)
- [x] CRUD de usuários
- [x] CRUD de clientes
- [x] CRUD de veículos
- [x] CRUD de mecânicos
- [x] CRUD de serviços
- [x] CRUD de peças com alerta de estoque baixo
- [x] CRUD de ordens de serviço com status e garantia
- [x] Interface responsiva com modais

---

## 🏗️ Estrutura do projeto (MVC)

```
Oficina-Mecanica/
├── oficina.sql                  # Script de criação do banco
├── sistema/
│   ├── config/
│   │   └── conexao.php          # Conexão com o banco de dados
│   ├── app/
│   │   ├── controllers/         # Recebem requisições e aplicam regras
│   │   │   ├── logincontroller.php
│   │   │   ├── logoutcontroller.php
│   │   │   ├── usuariocontroller.php
│   │   │   ├── ClienteController.php
│   │   │   ├── VeiculoController.php
│   │   │   ├── OrdemController.php
│   │   │   ├── PecaController.php
│   │   │   ├── MecanicoController.php
│   │   │   └── ServicoController.php
│   │   ├── models/              # Acesso ao banco de dados
│   │   │   ├── AuthModel.php
│   │   │   ├── UsuarioModel.php
│   │   │   ├── ClienteModel.php
│   │   │   ├── VeiculoModel.php
│   │   │   ├── OrdemModel.php
│   │   │   ├── PecaModel.php
│   │   │   ├── MecanicoModel.php
│   │   │   └── ServicoModel.php
│   │   └── views/               # Páginas PHP com sessão
│   │       ├── login.php
│   │       ├── cadastrar.php
│   │       ├── dashboard.php
│   │       ├── ordens.php
│   │       └── pecas.php
│   └── public/                  # Páginas HTML públicas
│       ├── index.html
│       ├── login.html
│       ├── script.js
│       ├── clientes.html / clientes.js
│       ├── veiculos.html / veiculos.js
│       ├── mecanicos.html / mecanicos.js
│       ├── servicos.html / servicos.js
│       ├── ordens.html / ordens.js
│       └── pecas.html / pecas.js
```

---

## 🚀 Como rodar localmente

### Pré-requisitos
- XAMPP instalado (Apache + MySQL)

### Passos

1. Clone o repositório dentro da pasta `htdocs`:
```bash
git clone https://github.com/LanneFV/Oficina-Mecanica.git
```

2. Inicie o Apache e o MySQL pelo XAMPP.

3. Acesse o phpMyAdmin e crie o banco:
```sql
CREATE DATABASE oficina;
```

4. Importe o arquivo `oficina.sql` no banco criado.

5. **No Linux com XAMPP**, edite `sistema/config/conexao.php` e use:
```php
$conn = new mysqli("127.0.0.1", "root", "", "oficina");
```

6. Acesse o sistema:
```
http://localhost/Oficina-Mecanica/sistema/public/index.html
```

---

## 📅 Entrega

**Prazo:** 15/06/2026  
**Disciplina:** Banco de Dados — Prof. Moises Silva de Sousa  
**Instituição:** IDP — Instituto de Ensino e Pesquisa
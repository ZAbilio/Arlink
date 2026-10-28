# ARLINK — Sistema de Gestão de Manutenções

O **ARLINK** é um sistema web desenvolvido como Trabalho de Conclusão de Curso (TCC) do curso Técnico em Desenvolvimento de Sistemas.

O projeto tem como objetivo facilitar o gerenciamento de manutenções de aparelhos de ar-condicionado, permitindo organizar informações de clientes, equipamentos, técnicos e serviços realizados.

Com o sistema, é possível acompanhar o histórico dos aparelhos e consultar as próximas manutenções, tornando o atendimento mais organizado.

## Funcionalidades

- **Login:** acesso ao sistema por e-mail e senha.
- **Dashboard:** visão geral dos clientes, aparelhos e manutenções.
- **Clientes:** cadastro, consulta, edição e exclusão.
- **Aparelhos:** gerenciamento dos equipamentos vinculados aos clientes.
- **Manutenções:** registro de serviços, diagnósticos, peças utilizadas e valores.
- **Histórico:** consulta dos serviços realizados em cada aparelho.
- **Busca:** pesquisa de clientes, equipamentos e manutenções.
- **Técnicos:** gerenciamento dos usuários responsáveis pelos atendimentos.

## Tecnologias utilizadas

| Tecnologia | Utilização |
|---|---|
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização e organização visual |
| JavaScript | Interações básicas |
| PHP | Processamento das informações e funcionalidades |
| MySQL/MariaDB | Armazenamento dos dados |
| Apache | Servidor web local |
| XAMPP | Ambiente de desenvolvimento e execução |

O sistema utiliza **PHP procedural**, mantendo a estrutura do código simples e acessível para estudo.

## Como instalar

### 1. Preparar o ambiente

Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html), com Apache, PHP 8 ou superior e MySQL/MariaDB.

### 2. Copiar o projeto

Baixe os arquivos do repositório e coloque a pasta do sistema em:

`C:\xampp\htdocs\arlink`

A pasta deve conter os arquivos PHP e as pastas `assets` e `includes`.

### 3. Iniciar os serviços

Abra o XAMPP Control Panel e inicie:

- Apache
- MySQL

### 4. Configurar o banco de dados

1. Acesse `http://localhost/phpmyadmin`.
2. Abra a aba **SQL**.
3. Copie o conteúdo do arquivo `arlink_db.sql`.
4. Execute o script para criar o banco e suas tabelas.

O banco utilizado pelo sistema chama-se `arlink_db`.

Este procedimento é destinado a instalações novas de demonstração. Não execute o script sobre um banco que contenha dados importantes sem antes fazer backup.

### 5. Abrir o sistema

Com Apache e MySQL iniciados, acesse:

`http://localhost/arlink/`

Caso o Apache utilize a porta 8081:

`http://localhost:8081/arlink/`

### 6. Primeiro acesso

Credenciais utilizadas no ambiente de demonstração local:

- **E-mail:** `admin@arlink.com`
- **Senha:** `admin123`

Essa conta é destinada exclusivamente à demonstração. Altere a senha após o primeiro acesso e não publique o sistema na internet com essas credenciais.

## Estrutura do projeto

```text
arlink/
├── assets/
│   ├── bootstrap/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── conexao.php
│   ├── sessao.php
│   └── ...
├── index.php
├── login.php
├── dashboard.php
├── clientes.php
├── aparelhos.php
├── manutencoes.php
├── historico.php
├── tecnicos.php
├── arlink_db.sql
└── README.md
```

Além das páginas principais, o projeto possui arquivos auxiliares para formulários, validações e outras operações.

## Banco de dados

O banco de dados do ARLINK é composto por quatro tabelas principais:

- `tecnicos`: usuários responsáveis pelos atendimentos.
- `clientes`: informações dos proprietários dos equipamentos.
- `aparelhos`: equipamentos vinculados aos clientes.
- `manutencoes`: serviços registrados para cada aparelho e técnico.

Essas tabelas utilizam chaves primárias e estrangeiras para relacionar as informações e manter a organização dos registros.

## Sobre o desenvolvimento

O ARLINK foi desenvolvido por estudantes do curso Técnico em Desenvolvimento de Sistemas da ETEC Rodrigues de Abreu, em Bauru/SP.

O projeto utiliza os conhecimentos adquiridos durante o curso, incluindo desenvolvimento web, programação PHP, estilização com CSS e modelagem de banco de dados.

Foram utilizadas ferramentas de apoio e inteligência artificial durante o desenvolvimento, com revisão e testes do sistema pela equipe.

**Projeto acadêmico — TCC 2026.**

O ARLINK é um protótipo para demonstração e aprendizado, não uma aplicação preparada para hospedagem pública em produção.
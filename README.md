<h1 align="center">Bem-vindo ao PROJECT API </h1>

# Biblioteca API

API RESTful para gerenciamento de uma biblioteca, desenvolvida com Laravel e integrada com Amazon SQS para processamento de filas.

## Índice

1. [Requisitos](#requisitos)
2. [Instalação](#instalação)
3. [Configuração](#configuração)
4. [Executando a Aplicação](#executando-a-aplicação)
5. [Documentação da API](#documentação-da-api)
   - [Autenticação](#autenticação)
   - [Livros](#livros)
   - [Empréstimos](#empréstimos)
6. [Testes](#testes)
7. [Integração com AWS SQS](#integração-com-aws-sqs)
8. [Deployment](#deployment)

## Requisitos

- PHP 8.3+
- Composer
- MySQL 5.7+ ou PostgreSQL 9.6+
- Redis
- Conta AWS (para SQS)

<h1 align="center">Olá 👋, eu sou Ramon Mendes - Desenvolvedor de Software</h1>
<h3 align="center">Um desenvolvedor back-end apaixonado por tecnologia</h3>

- 🔭 Atualmente estou trabalhando no [desenvolvimento de projetos Back-end](https://github.com/RamonSouzaDev/To-Do-List-)

- 🌱 Estou atualmente aprendendo **Arquitetura e Engenharia de Software**

- 📫 Como me encontrar **dwmom@hotmail.com**

   Acabei me empolgando e desenvolvendo, mesmo após a data de entrega.

<h3 align="left"> Vamos nos conectar:</h3>

<p align="left">
<a href="https://linkedin.com/in/https://www.linkedin.com/in/ramon-mendes-b44456164/" target="blank"><img align="center" src="https://raw.githubusercontent.com/rahuldkjain/github-profile-readme-generator/master/src/images/icons/Social/linked-in-alt.svg" alt="https://www.linkedin.com/in/ramon-mendes-b44456164/" height="30" width="40" /></a>
</p>

<h3 align="left">Linguagens e Ferramentas</h3>
<p align="left"> 
 <a href="https://hadoop.apache.org/" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/apache_hadoop/apache_hadoop-icon.svg" alt="hadoop" width="40" height="40"/> </a> <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="javascript" width="40" height="40"/> </a> </a> <a href="https://www.linux.org/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/linux/linux-original.svg" alt="linux" width="40" height="40"/> </a> <a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/> </a> <a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/> </a> </p>
<a href="https://www.docker.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/docker/docker-original-wordmark.svg" alt="docker" width="40" height="40"/> 


- **Instalando o Projeto**


1. Clone o repositório:
git clone git@github.com:RamonSouzaDev/project-api.git

2. Entre na pasta do projeto
cd "project-api"

**Executando usando docker** <p align="left"> <a href="https://www.docker.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/docker/docker-original-wordmark.svg" alt="docker" width="40" height="40"/> </a>
<br>

3. Execute os comandos para iniciar o ambiente de backend
cd "project-api"
no terminal, execute o comando: docker compose up --build

6. Abra uma terceira aba no seu terminal e execute o comando
Execute este comando dentro da pasta "backend-banking" para gerar as bibliotecas e gerar o composer para o Laravel
./start-backend.sh

## Executando a Aplicação

1. Inicie o worker para processar jobs da fila:
   ```
   php artisan queue:work sqs
   ```

## Documentação da API

### Autenticação

Todas as rotas da API, exceto registro e login, requerem autenticação via token Bearer.

#### Registro

- **URL:** `/api/register`
- **Método:** `POST`
- **Parâmetros do corpo:**
  ```json
  {
    "name": "Nome do Usuário",
    "email": "usuario@example.com",
    "password": "senha123"
  }
  ```
- **Resposta de Sucesso:**
  ```json
  {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer"
  }
  ```

#### Login

- **URL:** `/api/login`
- **Método:** `POST`
- **Parâmetros do corpo:**
  ```json
  {
    "email": "usuario@example.com",
    "password": "senha123"
  }
  ```
- **Resposta de Sucesso:**
  ```json
  {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer"
  }
  ```

### Livros

#### Listar Livros

- **URL:** `/api/books`
- **Método:** `GET`
- **Headers:** `Authorization: Bearer {seu_token}`
- **Resposta de Sucesso:**
  ```json
  [
    {
      "id": 1,
      "title": "O Senhor dos Anéis",
      "author": "J.R.R. Tolkien",
      "published_year": 1954
    },
  ]
  ```

#### Criar Livro

- **URL:** `/api/books`
- **Método:** `POST`
- **Headers:** `Authorization: Bearer {seu_token}`
- **Parâmetros do corpo:**
  ```json
  {
    "title": "1984",
    "author": "George Orwell",
    "published_year": 1949
  }
  ```
- **Resposta de Sucesso:**
  ```json
  {
    "id": 2,
    "title": "1984",
    "author": "George Orwell",
    "published_year": 1949
  }
  ```

### Empréstimos

#### Criar Empréstimo

- **URL:** `/api/loans`
- **Método:** `POST`
- **Headers:** `Authorization: Bearer {seu_token}`
- **Parâmetros do corpo:**
  ```json
  {
    "book_id": 1,
    "user_id": 1,
    "due_date": "2024-09-30"
  }
  ```
- **Resposta de Sucesso:**
  ```json
  {
    "id": 1,
    "book_id": 1,
    "user_id": 1,
    "due_date": "2024-09-30",
    "status": "active"
  }
  ```

#### Listar Empréstimos

- **URL:** `/api/loans`
- **Método:** `GET`
- **Headers:** `Authorization: Bearer {seu_token}`
- **Resposta de Sucesso:**
  ```json
  [
    {
      "id": 1,
      "book_id": 1,
      "user_id": 1,
      "due_date": "2024-09-30",
      "status": "active"
    },
  ]
  ```

## Testes

Execute os testes com:

```
php artisan test
```

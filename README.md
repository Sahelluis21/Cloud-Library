# Cloud Library

> Sistema pessoal de armazenamento e compartilhamento de arquivos com foco em privacidade, controle de acesso e arquitetura distribuída.

A **Cloud Library** é um sistema privado de armazenamento e compartilhamento de arquivos, desenvolvido para oferecer controle total sobre dados e infraestrutura.

A aplicação permite organizar e compartilhar arquivos de forma segura, operando em ambiente isolado da internet pública e acessível apenas mediante autenticação adequada.

O projeto prioriza segurança, segmentação de serviços e arquitetura distribuída, utilizando containers e proxy reverso para garantir controle, escalabilidade e proteção de dados.

O frontend ainda está em evolução, sendo o foco principal do projeto a arquitetura backend e a infraestrutura.

---
## 🎥 Demonstração

![Demonstração da Cloud Library](https://github.com/Sahelluis21/Cloud-Library/blob/main/docs/assets/Cloud%20Library.gif)
---

## 🚀 Stack Tecnológica

- **Backend:** Laravel (PHP) + JavaScript  
- **Banco de Dados:** PostgreSQL  
- **Infraestrutura:** Docker  
- **Orquestração:** Docker Swarm *(em fase de testes)*  
- **Servidor Web / HTTPS:** NGINX  
- **Arquitetura:** Sistema distribuído e containerizado  

---

## 🏗️ Arquitetura do Sistema

O sistema é distribuído em múltiplos serviços isolados:

- Container da aplicação (Laravel)
- Container do banco de dados (PostgreSQL)
- Container NGINX como proxy reverso com HTTPS
- Estrutura preparada para deploy em Docker Swarm

Separação clara entre:

- Camada de aplicação
- Camada de banco de dados
- Camada de infraestrutura
- Configurações por ambiente

---

## 📌 Status do Projeto

- Docker Compose: Estável para desenvolvimento
- Docker Swarm: Em fase de testes e validação
- Frontend: Em desenvolvimento inicial

---

### 📋 Pré-requisitos

- Docker instalado
- Docker Compose habilitado
- Docker Swarm inicializado *(opcional)*


### 🐧 Guia de Implantação - Ubuntu

Este procedimento foi validado em ambiente Ubuntu.

---

### 1️⃣ Clonar o repositório

```bash
git clone https://github.com/Sahelluis21/Cloud-Library.git
cd Cloud-Library

2️⃣ Construir e iniciar os containers
docker compose up --build

3️⃣ Acessar o container da aplicação
docker exec -it cloud-library-php-app-1 bash

4️⃣ Reinstalar dependências do Composer
rm -rf vendor
composer install
5️⃣ Sair do container
exit
6️⃣ Ajustar permissões da pasta de uploads
sudo chown -R $USER:$USER uploads
sudo chmod -R 775 uploads

Fim da Implantação
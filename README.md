# Cloud-Library
Biblioteca de Arquivos Compartilhados em Rede

# 🗂️ Biblioteca de Arquivos em Nuvem

Uma aplicação web leve e segura para **armazenamento e compartilhamento de arquivos** em rede local ou servidores próprios.

Desenvolvido com **PHP puro**, **PostgreSQL** e empacotado via **Docker**, o projeto surge como uma **alternativa viável e privada a serviços de armazenamento em nuvem**, ideal para empresas, equipes ou instituições que lidam com **dados sensíveis** e desejam manter **controle total sobre seus arquivos**.

---

## Funcionalidades

- 📁 Bibliotecas pessoais para cada usuário
- 🤝 Área pública de compartilhamento entre membros da equipe
- 🔐 Autenticação segura via sessões PHP
  
---

## ✅ Por que usar este sistema?

- 🔒 **Privacidade**: Todos os arquivos ficam sob o controle da equipe, sem depender de servidores externos.
- 🛠️ **Autonomia**: Roda em qualquer servidor com Docker, sem necessidade de serviços de terceiros.
- 💡 **Transparência e Simplicidade**: Código aberto, fácil de entender e adaptar.
- 🖥️ **Infraestrutura leve**: Ideal para rodar em máquinas simples, servidores internos ou VPS.

---

## 🛠️ Tecnologias Utilizadas

- **PHP (puro)** no backend
- **PostgreSQL** como banco de dados relacional
- **HTML + CSS puro** com layout responsivo e animações
- **Docker + Nginx** para orquestração de ambiente de produção

---
📂 Estrutura do Projeto

📁 php/                # Código-fonte PHP
📁 nginx/              # Configurações do servidor Nginx
📁 initdb/             # Scripts de criação do banco PostgreSQL
📁 uploads/            # Diretório onde os arquivos são armazenados
📄 docker-compose.yml  # Orquestração dos containers
---
## 📦 Como Executar com Docker

```bash
git clone https://github.com/Sahelluis21/Cloud-Library
cd Cloud-Library
docker-compose up --build


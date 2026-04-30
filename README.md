# 📦 Gerenciamento de Produtos - Desafio Técnico

Este projeto é uma aplicação Full-Stack desenvolvida para o gerenciamento de produtos, focada em **Arquitetura Limpa**, **SOLID** e **Qualidade de Software**. A solução atende aos requisitos de um CRUD completo e uma API RESTful protegida.

## 🏛️ Arquitetura e Diferenciais Técnicos

Para este desafio, optei por uma estrutura que demonstra maturidade técnica de nível Pleno/Sênior:

* **Repository Pattern:** Implementação de `ProductRepositoryInterface` para desacoplar a persistência de dados (Eloquent) da lógica de negócio.
* **Service Layer:** Toda a regra de negócio está centralizada na `ProductService`, mantendo os controllers enxutos e seguindo o princípio de Responsabilidade Única (SRP).
* **PestPHP:** Utilização do framework de testes mais moderno do ecossistema Laravel (conforme requisitos da vaga).
* **Documentação Swagger:** API totalmente documentada e testável via interface interativa.
* **Monitoramento:** Configuração de Redis e Worker para processamento de filas (Queues/Jobs).

## 🚀 Tecnologias Utilizadas

* **Backend:** PHP 8.3 + Laravel 11
* **Frontend:** Vue 3 (Composition API) + Inertia.js + Pinia (Estado Global)
* **Infra:** Docker & Docker Compose
* **Banco de Dados:** MySQL 8.0 & Redis
* **Testes:** PestPHP
* **UI/UX:** Estética baseada no Padrão Digital de Governo (Gov.br)

## 🛠️ Como Executar o Projeto

Siga os passos abaixo para subir o ambiente via Docker:

1. **Clonar e Acessar:**
   ```bash
   git clone git@github.com:maaclrd/First.git
   cd First
   Subir Infraestrutura:

Bash
docker-compose up -d --build
Configuração Inicial (Executar no container app):

Bash
docker-compose exec app sh -lc "composer install && npm install"
docker-compose exec app sh -lc "cp .env.example .env && php artisan key:generate"
docker-compose exec app sh -lc "php artisan migrate --force"
docker-compose exec app sh -lc "npm run build"
Gerar Documentação da API:

Bash
docker-compose exec app sh -lc "php artisan l5-swagger:generate"
🔗 Endpoints Principais
Aplicação Web: http://localhost:8080

Documentação Swagger (API): http://localhost:8080/api/documentation

✅ Testes Automatizados
Para rodar a suíte de testes (Feature e Unit):

Bash
docker-compose exec app sh -lc "php artisan test"

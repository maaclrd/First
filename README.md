Desafio Técnico: Gestão de Produtos (Laravel + Vue 3)
Este projeto consiste em um sistema para gerenciamento de produtos, desenvolvido como parte de um processo seletivo para Desenvolvedor PHP Pleno. A aplicação utiliza uma arquitetura moderna e escalável, focada em boas práticas de desenvolvimento e qualidade de código.

🚀 Tecnologias Utilizadas
Backend: PHP 8.3 e Laravel 11.

Frontend: Vue.js 3 (Composition API), Inertia.js e Pinia (Estado Global).

Banco de Dados: MySQL 8.0 e Redis (Cache/Fila).

Infraestrutura: Docker e Docker Compose.

Testes: PestPHP (Feature e Unit).

Documentação: Swagger (L5-Swagger).

UI/UX: Design baseado no Padrão Digital de Governo (Gov.br).

🏗️ Arquitetura e Padrões (SOLID)
A aplicação foi estruturada seguindo princípios de Clean Code e SOLID:

Repository Pattern: Desacoplamento da camada de persistência com o uso de Interfaces.

Service Layer: Centralização da lógica de negócio, garantindo que os Controllers permaneçam enxutos (Single Responsibility).

Dependency Injection: Inversão de dependência configurada via Service Providers.

API Resources: Padronização das respostas da API para consumo externo.

🛠️ Como Executar o Projeto
Siga os passos abaixo para subir o ambiente localmente utilizando Docker:

Clonar o repositório:

Bash
git clone git@github.com:maaclrd/First.git
cd First
Subir os containers:

Bash
docker-compose up -d --build
Instalar dependências e configurar o banco:

Bash
# Instalação do Composer e NPM
docker-compose exec app sh -lc "composer install && npm install"

# Configuração do ambiente
docker-compose exec app sh -lc "cp .env.example .env && php artisan key:generate"

# Execução das migrations
docker-compose exec app sh -lc "php artisan migrate"

# Build dos assets do frontend
docker-compose exec app sh -lc "npm run build"
Gerar Documentação Swagger:

Bash
docker-compose exec app sh -lc "php artisan l5-swagger:generate"
🔗 Acessos
Aplicação Web: http://localhost:8080

Documentação da API (Swagger): http://localhost:8080/api/documentation

✅ Testes Automatizados
Para validar a integridade da aplicação e as regras de negócio, utilize o comando:

Bash
docker-compose exec app sh -lc "php artisan test"

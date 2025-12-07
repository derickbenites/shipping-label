.PHONY: help install build up down restart logs ps shell mysql-shell test migrate fresh seed clear

# Variáveis
DOCKER_COMPOSE = docker-compose
APP_CONTAINER = shipping_app
MYSQL_CONTAINER = shipping_mysql

# Cor para output
BLUE = \033[0;34m
GREEN = \033[0;32m
YELLOW = \033[1;33m
NC = \033[0m # No Color

help: ## Mostra esta ajuda
	@echo "$(BLUE)Shipping Label - Comandos Disponíveis$(NC)"
	@echo "======================================="
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

install: ## Instala todas as dependências
	@echo "$(YELLOW)Instalando dependências do Composer...$(NC)"
	$(DOCKER_COMPOSE) run --rm $(APP_CONTAINER) composer install
	@echo "$(GREEN)✓ Dependências instaladas!$(NC)"

build: ## Constrói e inicia os containers
	@echo "$(YELLOW)Construindo containers...$(NC)"
	$(DOCKER_COMPOSE) up -d --build
	@echo "$(GREEN)✓ Containers construídos e iniciados!$(NC)"

up: ## Inicia os containers
	@echo "$(YELLOW)Iniciando containers...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Containers iniciados!$(NC)"

down: ## Para os containers
	@echo "$(YELLOW)Parando containers...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✓ Containers parados!$(NC)"

restart: down up ## Reinicia os containers

logs: ## Mostra logs de todos os containers
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Mostra logs do container da aplicação
	$(DOCKER_COMPOSE) logs -f $(APP_CONTAINER)

logs-mysql: ## Mostra logs do MySQL
	$(DOCKER_COMPOSE) logs -f $(MYSQL_CONTAINER)

ps: ## Lista status dos containers
	$(DOCKER_COMPOSE) ps

shell: ## Abre shell no container da aplicação
	docker exec -it $(APP_CONTAINER) bash

mysql-shell: ## Abre shell do MySQL
	docker exec -it $(MYSQL_CONTAINER) mysql -u shipping_user -pshipping_pass shipping_label

migrate: ## Executa migrations
	@echo "$(YELLOW)Executando migrations...$(NC)"
	docker exec $(APP_CONTAINER) php artisan migrate
	@echo "$(GREEN)✓ Migrations executadas!$(NC)"

fresh: ## Recria banco de dados (CUIDADO: apaga dados!)
	@echo "$(YELLOW)Recriando banco de dados...$(NC)"
	docker exec $(APP_CONTAINER) php artisan migrate:fresh
	@echo "$(GREEN)✓ Banco recriado!$(NC)"

seed: ## Executa seeders
	@echo "$(YELLOW)Executando seeders...$(NC)"
	docker exec $(APP_CONTAINER) php artisan db:seed
	@echo "$(GREEN)✓ Seeders executados!$(NC)"

fresh-seed: fresh seed ## Recria banco e executa seeders

test: ## Executa testes
	@echo "$(YELLOW)Executando testes...$(NC)"
	docker exec $(APP_CONTAINER) php artisan test
	@echo "$(GREEN)✓ Testes concluídos!$(NC)"

clear: ## Limpa todos os caches
	@echo "$(YELLOW)Limpando caches...$(NC)"
	docker exec $(APP_CONTAINER) php artisan config:clear
	docker exec $(APP_CONTAINER) php artisan cache:clear
	docker exec $(APP_CONTAINER) php artisan route:clear
	docker exec $(APP_CONTAINER) php artisan view:clear
	@echo "$(GREEN)✓ Caches limpos!$(NC)"

optimize: ## Otimiza a aplicação
	@echo "$(YELLOW)Otimizando aplicação...$(NC)"
	docker exec $(APP_CONTAINER) php artisan config:cache
	docker exec $(APP_CONTAINER) php artisan route:cache
	docker exec $(APP_CONTAINER) php artisan view:cache
	@echo "$(GREEN)✓ Aplicação otimizada!$(NC)"

composer-update: ## Atualiza dependências do Composer
	@echo "$(YELLOW)Atualizando Composer...$(NC)"
	docker exec $(APP_CONTAINER) composer update
	@echo "$(GREEN)✓ Composer atualizado!$(NC)"

permissions: ## Corrige permissões
	@echo "$(YELLOW)Corrigindo permissões...$(NC)"
	docker exec $(APP_CONTAINER) chown -R www-data:www-data storage bootstrap/cache
	docker exec $(APP_CONTAINER) chmod -R 775 storage bootstrap/cache
	@echo "$(GREEN)✓ Permissões corrigidas!$(NC)"

clean: ## Remove tudo (containers, volumes, imagens)
	@echo "$(YELLOW)Removendo tudo...$(NC)"
	$(DOCKER_COMPOSE) down -v --rmi all
	@echo "$(GREEN)✓ Tudo removido!$(NC)"

setup: build ## Setup completo do projeto
	@echo "$(YELLOW)Configurando projeto...$(NC)"
	@sleep 15
	@make migrate
	@make clear
	@echo "$(GREEN)✓ Setup concluído!$(NC)"
	@echo ""
	@echo "$(GREEN)Acesse: http://localhost:8000$(NC)"

status: ## Mostra informações do projeto
	@echo "$(BLUE)Status do Projeto$(NC)"
	@echo "=================="
	@echo ""
	@echo "$(YELLOW)Containers:$(NC)"
	@$(DOCKER_COMPOSE) ps
	@echo ""
	@echo "$(YELLOW)URLs:$(NC)"
	@echo "  Laravel API: http://localhost:8000"
	@echo "  Nginx:       http://localhost:80"
	@echo "  MySQL:       localhost:3306"
	@echo ""
	@echo "$(YELLOW)Database:$(NC)"
	@echo "  Database: shipping_label"
	@echo "  User:     shipping_user"
	@echo "  Password: shipping_pass"


# ⚡ Quick Start - Shipping Label

Guia rápido para começar a usar o projeto em **menos de 5 minutos**!

## 🚀 Opção 1: Setup Automático (RECOMENDADO)

```bash
# Executar script de setup
./setup.sh
```

Pronto! O script faz tudo automaticamente:
- ✅ Inicia Docker
- ✅ Constrói containers
- ✅ Configura banco de dados
- ✅ Executa migrations
- ✅ Limpa caches

## ⚡ Opção 2: Usando Make

```bash
# Setup completo
make setup

# Ou passo a passo:
make build      # Constrói e inicia containers
make migrate    # Executa migrations
```

Ver todos os comandos disponíveis:
```bash
make help
```

## 🔧 Opção 3: Manual

```bash
# 1. Iniciar Docker
sudo service docker start  # No WSL2

# 2. Construir e iniciar containers
docker-compose up -d --build

# 3. Aguardar MySQL (15 segundos)
sleep 15

# 4. Executar migrations
docker exec -it shipping_app php artisan migrate
```

## 🌐 Acessar Aplicação

Após o setup, acesse:

| Serviço | URL |
|---------|-----|
| 🐘 Laravel | http://localhost:8000 |
| 🌐 Nginx | http://localhost:80 |
| 🗄️ MySQL | localhost:3306 |

## 📝 Comandos Úteis

### Com Make (mais fácil)
```bash
make help           # Ver todos os comandos
make logs           # Ver logs
make shell          # Entrar no container
make test           # Rodar testes
make restart        # Reiniciar tudo
```

### Com Docker Compose
```bash
docker-compose ps              # Ver status
docker-compose logs -f         # Ver logs
docker-compose down            # Parar tudo
docker-compose up -d           # Iniciar tudo
```

### Laravel (dentro do container)
```bash
# Entrar no container
docker exec -it shipping_app bash

# Comandos Laravel
php artisan migrate
php artisan make:model Product
php artisan make:controller ProductController
php artisan test
```

## 🔐 Credenciais MySQL

```
Host:     mysql (ou localhost fora do Docker)
Port:     3306
Database: shipping_label
Username: shipping_user
Password: shipping_pass
```

## 🆘 Problemas Comuns

### Docker não está rodando
```bash
sudo service docker start
```

### Porta já em uso
Edite `.env` e mude `APP_PORT=8001`

### Erro de permissão
```bash
make permissions
# ou
chmod -R 775 storage bootstrap/cache
```

### MySQL não conecta
```bash
make logs-mysql        # Ver logs do MySQL
make restart           # Reiniciar tudo
```

## 📚 Mais Documentação

- [`README-INSTALACAO.md`](README-INSTALACAO.md) - Guia detalhado
- [`README.md`](README.md) - Documentação do Laravel

## 🎉 Pronto!

Seu projeto está rodando! 

Acesse: **http://localhost:8000**

Comece a desenvolver! 💻🚀


# 🚀 Instalação do Projeto Shipping Label

Sistema de etiquetas de envio desenvolvido com Laravel 12 e Docker.

## 📋 Pré-requisitos

- **Docker** (versão 20.10 ou superior)
- **Docker Compose** (versão 2.0 ou superior)
- **Git**

## 🔧 Instalação Rápida

### 1️⃣ Iniciar o Docker

No Windows com WSL2:
```bash
# Abra o Docker Desktop ou inicie o serviço
sudo service docker start
```

### 2️⃣ Verificar Instalação

Os arquivos já estão configurados! Verifique:
```bash
ls -la
```

Você deve ver:
- ✅ `docker-compose.yml` - Orquestração dos containers
- ✅ `Dockerfile` - Imagem do Laravel
- ✅ `.env` - Configurações do ambiente
- ✅ Pasta `app/`, `routes/`, `database/` - Laravel instalado

### 3️⃣ Iniciar os Containers

```bash
# Iniciar todos os serviços
docker-compose up -d --build
```

Este comando irá:
- 🗄️ Criar container MySQL 8.0
- 🐘 Criar container PHP 8.3 com Laravel
- 🌐 Criar container Nginx (opcional)

### 4️⃣ Aguardar MySQL Inicializar

```bash
# Verificar status dos containers
docker-compose ps

# Ver logs
docker-compose logs -f mysql
```

Aguarde até ver: `ready for connections`

### 5️⃣ Executar Migrations

```bash
# Entrar no container da aplicação
docker exec -it shipping_app bash

# Rodar migrations
php artisan migrate

# Sair do container
exit
```

## 🌐 Acessando a Aplicação

Após iniciar os containers:

| Serviço | URL | Descrição |
|---------|-----|-----------|
| 🐘 **Laravel API** | http://localhost:8000 | API Backend |
| 🌐 **Nginx** | http://localhost:80 | Web Server |
| 🗄️ **MySQL** | localhost:3306 | Banco de Dados |

## 🔐 Credenciais do Banco de Dados

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=shipping_label
DB_USERNAME=shipping_user
DB_PASSWORD=shipping_pass
```

**Root Password:** `root_password`

## 📝 Comandos Úteis

### Docker Compose

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f app

# Rebuild containers
docker-compose up -d --build

# Remover tudo (incluindo volumes)
docker-compose down -v
```

### Laravel (dentro do container)

```bash
# Entrar no container
docker exec -it shipping_app bash

# Rodar migrations
php artisan migrate

# Criar migration
php artisan make:migration create_table_name

# Criar model
php artisan make:model ModelName -m

# Criar controller
php artisan make:controller ControllerName

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rodar testes
php artisan test
```

### Composer (dentro do container)

```bash
# Entrar no container
docker exec -it shipping_app bash

# Instalar dependências
composer install

# Adicionar pacote
composer require vendor/package

# Atualizar dependências
composer update
```

## 🗂️ Estrutura do Projeto

```
shipping-label/
├── app/                    # Código da aplicação Laravel
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Models Eloquent
│   └── ...
├── config/                # Configurações
├── database/              # Migrations, Seeds, Factories
│   ├── migrations/
│   └── seeders/
├── docker/                # Configurações Docker
│   └── nginx/            # Config Nginx
├── public/                # Ponto de entrada (index.php)
├── routes/                # Rotas da aplicação
│   ├── api.php           # Rotas API
│   └── web.php           # Rotas Web
├── storage/               # Arquivos gerados
├── tests/                 # Testes automatizados
├── .env                   # Variáveis de ambiente
├── docker-compose.yml     # Orquestração Docker
├── Dockerfile             # Imagem Laravel
└── artisan               # CLI do Laravel
```

## 🔄 Fluxo de Desenvolvimento

1. **Editar código** na sua IDE local
2. **Mudanças refletidas** automaticamente no container
3. **Rodar migrations/comandos** dentro do container
4. **Testar** via http://localhost:8000

## 🐛 Troubleshooting

### Docker não inicia

```bash
# Verificar se Docker está rodando
docker ps

# No WSL2, iniciar Docker
sudo service docker start

# Ou use Docker Desktop no Windows
```

### Erro de permissão no Laravel

```bash
docker exec -it shipping_app bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### MySQL não conecta

```bash
# Ver logs do MySQL
docker-compose logs mysql

# Reiniciar o serviço
docker-compose restart mysql

# Aguardar estar "healthy"
docker-compose ps
```

### Porta já em uso

Edite o arquivo `.env` e altere as portas:

```env
APP_PORT=8001
DB_PORT=3307
NGINX_PORT=8080
```

Depois reinicie:
```bash
docker-compose down
docker-compose up -d
```

## 🚀 Próximos Passos

1. ✅ Laravel instalado e rodando
2. ✅ MySQL configurado
3. 📝 Criar suas migrations
4. 🎨 Desenvolver controllers e models
5. 🧪 Escrever testes
6. 🚢 Deploy

## 📚 Recursos

- [Documentação Laravel](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 💡 Dicas

- Use **migrations** para versionar seu banco de dados
- Use **seeders** para dados iniciais
- Use **factories** para dados de teste
- Escreva **testes** para seu código
- Mantenha o **.env** fora do Git (já está no .gitignore)

## 🎉 Sucesso!

Seu ambiente de desenvolvimento Laravel com Docker está pronto! 

Para testar, acesse: http://localhost:8000

Você deve ver a página inicial do Laravel.


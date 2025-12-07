# 🚢 Shipping Label System

Sistema de gerenciamento de etiquetas de envio desenvolvido com **Laravel 12** e **Docker**.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker&logoColor=white)

## ✨ Features

- 🐘 **Laravel 12** - Framework PHP moderno e poderoso
- 🐳 **Docker** - Ambiente totalmente containerizado
- 🗄️ **MySQL 8.0** - Banco de dados robusto
- 🌐 **Nginx** - Web server de alta performance
- 🧪 **PHPUnit** - Testes automatizados
- 📦 **Composer** - Gerenciamento de dependências

## 🚀 Início Rápido

### Pré-requisitos

- Docker 20.10+
- Docker Compose 2.0+

### Instalação (3 minutos)

```bash
# 1. Clone o repositório (se ainda não fez)
git clone <seu-repositorio>
cd shipping-label

# 2. Execute o setup automático
./setup.sh
```

Ou com Make:

```bash
make setup
```

Ou manualmente:

```bash
# Iniciar Docker
sudo service docker start  # No WSL2

# Construir e iniciar
docker-compose up -d --build

# Aguardar MySQL (15s)
sleep 15

# Migrations
docker exec -it shipping_app php artisan migrate
```

### Acessar

| Serviço | URL | Descrição |
|---------|-----|-----------|
| 🐘 **Laravel API** | http://localhost:8000 | Backend API |
| 🌐 **Nginx** | http://localhost:80 | Web Server |
| 🗄️ **MySQL** | localhost:3306 | Database |

## 📖 Documentação

| Arquivo | Descrição |
|---------|-----------|
| [QUICK_START.md](QUICK_START.md) | ⚡ Início super rápido (3 min) |
| [README-INSTALACAO.md](README-INSTALACAO.md) | 📚 Guia detalhado de instalação |
| `make help` | 🛠️ Todos os comandos disponíveis |

## 🛠️ Comandos Úteis

### Make (Recomendado)

```bash
make help           # Lista todos os comandos
make up             # Inicia containers
make down           # Para containers
make logs           # Mostra logs
make shell          # Acessa container
make test           # Roda testes
make migrate        # Executa migrations
make fresh-seed     # Recria DB com dados
```

### Docker Compose

```bash
docker-compose ps              # Status dos containers
docker-compose logs -f         # Ver logs em tempo real
docker-compose down            # Parar todos os containers
docker-compose up -d --build   # Rebuild e iniciar
```

### Laravel (dentro do container)

```bash
# Entrar no container
docker exec -it shipping_app bash

# Dentro do container
php artisan migrate              # Rodar migrations
php artisan make:model Product   # Criar model
php artisan make:controller API/ProductController --api
php artisan test                 # Rodar testes
php artisan cache:clear          # Limpar cache
```

## 🗂️ Estrutura do Projeto

```
shipping-label/
├── app/                    # Código da aplicação
│   ├── Http/
│   │   └── Controllers/   # Controllers
│   ├── Models/            # Models Eloquent
│   └── ...
├── database/
│   ├── migrations/        # Migrations do banco
│   └── seeders/           # Seeders
├── docker/
│   └── nginx/             # Configurações Nginx
├── routes/
│   ├── api.php           # Rotas da API
│   └── web.php           # Rotas Web
├── tests/                 # Testes PHPUnit
├── .env                   # Variáveis de ambiente
├── docker-compose.yml     # Orquestração Docker
├── Dockerfile             # Imagem Laravel
├── Makefile              # Comandos Make
└── setup.sh              # Script de setup automático
```

## 🔐 Credenciais

### MySQL

```env
Host:     mysql (dentro do Docker) ou localhost (fora)
Port:     3306
Database: shipping_label
User:     shipping_user
Password: shipping_pass
Root:     root_password
```

## 🧪 Testes

```bash
# Rodar todos os testes
make test

# Ou manualmente
docker exec shipping_app php artisan test

# Com coverage
docker exec shipping_app php artisan test --coverage
```

## 🐛 Troubleshooting

### Docker não inicia
```bash
sudo service docker start
```

### Erro de permissão
```bash
make permissions
```

### MySQL não conecta
```bash
make logs-mysql
make restart
```

### Porta ocupada
Edite `.env` e mude as portas:
```env
APP_PORT=8001
DB_PORT=3307
```

## 📝 Desenvolvimento

### Criar um novo endpoint API

```bash
# 1. Criar migration
docker exec shipping_app php artisan make:migration create_products_table

# 2. Editar migration em database/migrations/

# 3. Criar model
docker exec shipping_app php artisan make:model Product

# 4. Criar controller
docker exec shipping_app php artisan make:controller API/ProductController --api

# 5. Adicionar rotas em routes/api.php

# 6. Rodar migration
docker exec shipping_app php artisan migrate

# 7. Criar testes
docker exec shipping_app php artisan make:test ProductTest
```

### Padrão de Código

O projeto usa:
- ✅ PSR-12 (código)
- ✅ Laravel Best Practices
- ✅ RESTful API Design

## 🚀 Deploy

Para produção, lembre-se de:

1. ✅ Alterar `APP_ENV=production` no `.env`
2. ✅ Alterar `APP_DEBUG=false`
3. ✅ Gerar nova `APP_KEY`
4. ✅ Configurar credenciais reais de banco
5. ✅ Otimizar: `make optimize`

## 📦 Tecnologias

- **Backend**: Laravel 12, PHP 8.3
- **Database**: MySQL 8.0
- **Web Server**: Nginx (Alpine)
- **Container**: Docker, Docker Compose
- **Testing**: PHPUnit
- **Package Manager**: Composer

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch: `git checkout -b feature/MinhaFeature`
3. Commit: `git commit -m 'Add: Minha feature'`
4. Push: `git push origin feature/MinhaFeature`
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT.

## 👨‍💻 Autor

Desenvolvido com ❤️ para gerenciamento de etiquetas de envio.

## 🔗 Links Úteis

- [Documentação Laravel](https://laravel.com/docs/12.x)
- [Docker Docs](https://docs.docker.com/)
- [MySQL Docs](https://dev.mysql.com/doc/)
- [Nginx Docs](https://nginx.org/en/docs/)

## 📞 Suporte

Para dúvidas ou problemas:
- 📖 Leia a [documentação](README-INSTALACAO.md)
- 🐛 Abra uma [issue](../../issues)
- 💬 Entre em contato

---

⭐ **Star** este projeto se ele foi útil para você!

🐛 Encontrou um bug? [Reporte aqui](../../issues)

💡 Tem uma sugestão? [Contribua!](../../pulls)

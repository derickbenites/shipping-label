# 📦 Shipping Label Generator

A web application for generating USPS shipping labels using the EasyPost API. Users can create, view, track, and manage their shipping labels with a modern, intuitive interface.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd shipping-label
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Add your EasyPost API key to `.env`**
```bash
EASYPOST_API_KEY=your_test_api_key_here
```
Get your free test API key at: https://www.easypost.com/

4. **Start the application**
```bash
docker-compose up -d --build
```

5. **Install dependencies and setup database**
```bash
docker exec shipping_app composer install
docker exec shipping_app php artisan key:generate
docker exec shipping_app php artisan migrate
docker exec shipping_app npm install
docker exec shipping_app npm run build
```

6. **Access the application**
- Frontend: http://localhost:8000
- Database: localhost:3307 (user: `shipping_user`, password: `shipping_pass`)

## 📚 Features

✅ **User Authentication** - Register and login  
✅ **Create Labels** - Generate USPS shipping labels with EasyPost  
✅ **View Rates** - Check shipping costs before creating labels  
✅ **Track Shipments** - Direct link to USPS tracking  
✅ **Print Labels** - PDF labels ready to print  
✅ **Manage Labels** - View history and cancel labels  
✅ **Search & Filter** - Find labels by tracking, status, or addresses  

## 🛠️ Tech Stack

**Backend:**
- Laravel 12 (PHP 8.3)
- MySQL 8.0
- EasyPost API

**Frontend:**
- Vue.js 3
- Inertia.js
- Tailwind CSS

**Infrastructure:**
- Docker
- Nginx

## 📝 Usage

### Create Your First Label

1. Register or login at http://localhost:8000
2. Click "New Label"
3. Fill in:
   - **From Address** (origin - US only)
   - **To Address** (destination - US only)
   - **Package Details** (weight in oz, dimensions in inches)
4. Optional: Click "Get Rates" to see available shipping options
5. Click "Create Label" to generate the shipping label
6. Print the label and attach it to your package

### US States Format
Use 2-letter state codes (e.g., CA, NY, TX). ZIP codes must be 5 digits.

## ⚙️ Configuration

### Environment Variables
Key variables in `.env`:

```bash
# Application
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=shipping_label
DB_USERNAME=shipping_user
DB_PASSWORD=shipping_pass

# EasyPost
EASYPOST_API_KEY=your_api_key_here
```

### Docker Ports
- **8000**: Laravel application
- **3307**: MySQL (external access)

## 🧪 Testing

**Test with EasyPost test addresses:**

```
From:
417 Montgomery Street
San Francisco, CA 94104

To:
179 N Harbor Dr
Redondo Beach, CA 90277

Package: 15.4 oz
```

## 🔧 Useful Commands

```bash
# Stop containers
docker-compose stop

# Restart containers
docker-compose restart

# View logs
docker-compose logs -f app

# Clear Laravel cache
docker exec shipping_app php artisan optimize:clear

# Rebuild frontend
docker exec shipping_app npm run build

# Access container shell
docker exec -it shipping_app bash
```

## 🐛 Troubleshooting

**Port 3306 already in use?**
→ Stop local MySQL or change `DB_PORT` in `docker-compose.yml`

**EasyPost API errors?**
→ Check your API key in `.env` and restart: `docker-compose restart app`

**Frontend not loading?**
→ Rebuild assets: `docker exec shipping_app npm run build`

**Database connection failed?**
→ Ensure MySQL is healthy: `docker-compose ps`

## 📁 Project Structure

```
shipping-label/
├── app/
│   ├── Http/Controllers/
│   │   └── ShippingLabelController.php
│   ├── Models/
│   │   └── ShippingLabel.php
│   └── Services/
│       └── EasyPostService.php
├── resources/
│   └── js/
│       └── Pages/
│           └── ShippingLabels/
│               ├── Index.vue
│               ├── Create.vue
│               └── Show.vue
├── routes/
│   └── web.php
├── docker-compose.yml
└── .env
```

## 📄 License

This project is open-source software.

---

**Need help?** Check the EasyPost documentation: https://docs.easypost.com/


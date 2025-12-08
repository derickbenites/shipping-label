# 📦 Shipping Label API Documentation

API para gerenciamento de etiquetas de envio USPS usando EasyPost.

## 🔐 Autenticação

Esta API usa **Laravel Sanctum** para autenticação baseada em tokens.

### Base URL
```
http://localhost:8000/api
```

### Headers Necessários

Para rotas protegidas, inclua o token no header:
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
Accept: application/json
```

---

## 📋 Endpoints

### 1. Autenticação

#### Registrar Novo Usuário
```http
POST /api/auth/register
```

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@example.com",
      "created_at": "2025-12-07T20:00:00.000000Z"
    },
    "token": "1|xxx...xxx",
    "token_type": "Bearer"
  }
}
```

#### Login
```http
POST /api/auth/login
```

**Body:**
```json
{
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "2|xxx...xxx",
    "token_type": "Bearer"
  }
}
```

#### Logout
```http
POST /api/auth/logout
```
*Requer autenticação*

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### Obter Usuário Atual
```http
GET /api/auth/me
```
*Requer autenticação*

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com"
  }
}
```

---

### 2. Etiquetas de Envio

#### Listar Etiquetas do Usuário
```http
GET /api/shipping-labels
```
*Requer autenticação*

**Query Parameters:**
- `page` (optional): Número da página (padrão: 1)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "easypost_shipment_id": "shp_xxx",
        "from_name": "Empresa ABC",
        "from_city": "Los Angeles",
        "from_state": "CA",
        "to_name": "Cliente XYZ",
        "to_city": "New York",
        "to_state": "NY",
        "weight": 16.00,
        "carrier": "USPS",
        "service": "First",
        "rate": 7.33,
        "tracking_code": "9400...xxx",
        "label_url": "https://...",
        "status": "purchased",
        "created_at": "2025-12-07T20:00:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

#### Criar Nova Etiqueta
```http
POST /api/shipping-labels
```
*Requer autenticação*

**Body:**
```json
{
  "from_name": "Empresa ABC",
  "from_company": "ABC Corp",
  "from_street1": "123 Main St",
  "from_street2": "Suite 100",
  "from_city": "Los Angeles",
  "from_state": "CA",
  "from_zip": "90001",
  "from_phone": "310-555-1234",
  
  "to_name": "Cliente XYZ",
  "to_company": "XYZ Inc",
  "to_street1": "456 Broadway",
  "to_street2": "Apt 5B",
  "to_city": "New York",
  "to_state": "NY",
  "to_zip": "10001",
  "to_phone": "212-555-5678",
  
  "weight": 16,
  "length": 12,
  "width": 8,
  "height": 6
}
```

**Notas Importantes:**
- ✅ **Apenas endereços dos EUA são aceitos**
- ✅ `from_state` e `to_state` devem ser códigos de 2 letras (ex: CA, NY)
- ✅ `from_zip` e `to_zip` no formato 12345 ou 12345-6789
- ✅ `weight` em **onças** (oz) - 16 oz = 1 lb
- ✅ `length`, `width`, `height` em **polegadas** (opcional)
- ✅ Máximo de peso: 1120 oz (70 lbs)

**Response (201):**
```json
{
  "success": true,
  "message": "Shipping label created successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "easypost_shipment_id": "shp_xxx",
    "tracking_code": "9400...xxx",
    "carrier": "USPS",
    "service": "First",
    "rate": 7.33,
    "label_url": "https://easypost-files.s3.amazonaws.com/...",
    "label_pdf_url": "https://easypost-files.s3.amazonaws.com/.../label.pdf",
    "label_png_url": "https://easypost-files.s3.amazonaws.com/.../label.png",
    "status": "purchased",
    "from_name": "Empresa ABC",
    "from_city": "Los Angeles",
    "from_state": "CA",
    "to_name": "Cliente XYZ",
    "to_city": "New York",
    "to_state": "NY",
    "weight": 16.00,
    "created_at": "2025-12-07T20:00:00.000000Z"
  }
}
```

#### Visualizar Etiqueta Específica
```http
GET /api/shipping-labels/{id}
```
*Requer autenticação*

**Response (200):**
```json
{
  "success": true,
  "data": { ... }
}
```

#### Cancelar/Reembolsar Etiqueta
```http
DELETE /api/shipping-labels/{id}
```
*Requer autenticação*

**Response (200):**
```json
{
  "success": true,
  "message": "Shipping label cancelled and refunded successfully"
}
```

**Nota:** Nem todas as etiquetas são elegíveis para reembolso. Consulte as políticas do USPS.

#### Obter Tarifas (sem comprar)
```http
POST /api/shipping-labels/rates
```
*Requer autenticação*

**Body:** (mesmos campos que criar etiqueta)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": "rate_xxx",
      "carrier": "USPS",
      "service": "First",
      "rate": "7.33",
      "delivery_days": 2
    },
    {
      "id": "rate_yyy",
      "carrier": "USPS",
      "service": "Priority",
      "rate": "9.45",
      "delivery_days": 1
    }
  ]
}
```

---

## 📝 Exemplos de Uso

### Com cURL

#### 1. Registrar
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'
```

#### 2. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "joao@example.com",
    "password": "senha123"
  }'
```

#### 3. Criar Etiqueta
```bash
curl -X POST http://localhost:8000/api/shipping-labels \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{
    "from_name": "Empresa ABC",
    "from_street1": "123 Main St",
    "from_city": "Los Angeles",
    "from_state": "CA",
    "from_zip": "90001",
    "to_name": "Cliente XYZ",
    "to_street1": "456 Broadway",
    "to_city": "New York",
    "to_state": "NY",
    "to_zip": "10001",
    "weight": 16
  }'
```

#### 4. Listar Etiquetas
```bash
curl -X GET http://localhost:8000/api/shipping-labels \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

---

## ⚠️ Códigos de Erro

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Requisição inválida |
| 401 | Não autenticado |
| 404 | Não encontrado |
| 422 | Erros de validação |
| 500 | Erro no servidor |

### Exemplo de Erro de Validação (422)
```json
{
  "message": "The from state field must be 2 characters.",
  "errors": {
    "from_state": [
      "The from state field must be 2 characters."
    ]
  }
}
```

---

## 🔒 Segurança

- ✅ Tokens são únicos por sessão
- ✅ Usuários só podem ver suas próprias etiquetas
- ✅ Senhas são hash com bcrypt
- ✅ Validação rigorosa de entrada
- ✅ Proteção contra SQL Injection

---

## 📊 Limites

- **Paginação:** 20 itens por página
- **Peso máximo:** 1120 oz (70 lbs)
- **Apenas endereços dos EUA**

---

## 🧪 Teste com EasyPost

Para testar, você precisa de uma **chave de API de teste** do EasyPost:

1. Crie uma conta em: https://www.easypost.com/signup
2. Obtenha sua chave de API de teste no dashboard
3. Adicione no arquivo `.env`:
```env
EASYPOST_API_KEY=EZTK...seu_token_de_teste
```

**Nota:** Chaves de teste começam com `EZTK`. Não use chaves de produção (`EZAK`) em desenvolvimento.

---

## 🎨 Campos de Endereço

### Campos Obrigatórios
- `name`: Nome do remetente/destinatário
- `street1`: Endereço (linha 1)
- `city`: Cidade
- `state`: Estado (2 letras, ex: CA)
- `zip`: CEP (formato: 12345 ou 12345-6789)

### Campos Opcionais
- `company`: Nome da empresa
- `street2`: Complemento
- `phone`: Telefone

---

## 📦 Detalhes do Pacote

### Peso (weight)
- **Unidade:** Onças (oz)
- **Conversão:** 1 libra = 16 onças
- **Exemplo:** Um pacote de 2 lbs = 32 oz

### Dimensões (opcional)
- **length:** Comprimento em polegadas
- **width:** Largura em polegadas  
- **height:** Altura em polegadas

Se não informado, dimensões padrão serão usadas.

---

## 🖨️ Impressão de Etiquetas

As etiquetas são retornadas em 3 formatos:

1. **label_url**: URL da imagem (PNG padrão)
2. **label_pdf_url**: URL do PDF (melhor para impressão)
3. **label_png_url**: URL do PNG de alta resolução

**Recomendação:** Use `label_pdf_url` para impressão em impressoras térmicas ou laser.

---

## 📞 Suporte

Para dúvidas sobre:
- **API:** Veja este documento
- **EasyPost:** https://docs.easypost.com/
- **Problemas:** Abra uma issue no repositório

---

**Desenvolvido com ❤️ usando Laravel 12 + EasyPost API**


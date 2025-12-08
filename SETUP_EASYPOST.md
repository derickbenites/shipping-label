# 🚀 Configuração EasyPost - Guia Rápido

## 1️⃣ Criar Conta EasyPost

1. Acesse: https://www.easypost.com/signup
2. Crie uma conta gratuita (modo de teste)
3. Verifique seu email

## 2️⃣ Obter API Key de Teste

1. Faça login no dashboard: https://www.easypost.com/account/api-keys
2. Copie sua **Test API Key** (começa com `EZTK...`)
3. ⚠️ **NÃO use Production Key em desenvolvimento!**

## 3️⃣ Configurar no Laravel

### Adicionar no arquivo `.env`:

```bash
EASYPOST_API_KEY=EZTK_seu_token_de_teste_aqui
```

### Verificar instalação:

```bash
# Entrar no container
docker exec -it shipping_app bash

# Verificar se EasyPost está instalado
php -r "echo class_exists('EasyPost\EasyPostClient') ? 'OK' : 'ERROR';"
```

## 4️⃣ Testar a API

### Criar um usuário de teste:

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Teste",
    "email": "teste@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'
```

### Criar uma etiqueta de teste:

```bash
# Salve o token recebido no registro
TOKEN="seu_token_aqui"

curl -X POST http://localhost:8000/api/shipping-labels \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "from_name": "Teste Sender",
    "from_street1": "417 Montgomery St",
    "from_city": "San Francisco",
    "from_state": "CA",
    "from_zip": "94104",
    "to_name": "Test Recipient",
    "to_street1": "1 E 161 St",
    "to_city": "The Bronx",
    "to_state": "NY",
    "to_zip": "10451",
    "weight": 16
  }'
```

## 5️⃣ Endereços de Teste do EasyPost

O EasyPost fornece endereços de teste que sempre funcionam:

### Origem (San Francisco):
```json
{
  "from_name": "EasyPost Test",
  "from_street1": "417 Montgomery St",
  "from_street2": "Floor 5",
  "from_city": "San Francisco",
  "from_state": "CA",
  "from_zip": "94104",
  "from_phone": "415-456-7890"
}
```

### Destino (New York):
```json
{
  "to_name": "Test Recipient",
  "to_street1": "1 E 161 St",
  "to_city": "The Bronx",
  "to_state": "NY",
  "to_zip": "10451",
  "to_phone": "917-123-4567"
}
```

## 6️⃣ Verificar Logs

Se houver erros:

```bash
# Ver logs do Laravel
docker exec shipping_app tail -f storage/logs/laravel.log

# Ver logs do container
docker-compose logs -f app
```

## 7️⃣ Troubleshooting

### Erro: "EasyPost API key not configured"
```bash
# Verifique se está no .env
cat .env | grep EASYPOST

# Limpe o cache de config
docker exec shipping_app php artisan config:clear
```

### Erro: "Class 'EasyPost\EasyPostClient' not found"
```bash
# Reinstale o pacote
docker exec shipping_app composer require easypost/easypost-php

# Verifique se foi instalado
docker exec shipping_app composer show | grep easypost
```

### Erro: "Failed to create shipment"
- ✅ Verifique se os endereços são dos EUA
- ✅ Verifique se os estados são códigos de 2 letras (CA, NY)
- ✅ Verifique se o ZIP code está no formato correto
- ✅ Verifique se o peso está em onças (oz)

## 8️⃣ Limites do Modo de Teste

No modo de teste (Test API Key):

✅ **Permitido:**
- Criar etiquetas ilimitadas
- Testar todos os carriers (USPS, UPS, FedEx)
- Validar endereços
- Gerar tracking codes

❌ **NÃO permitido:**
- Etiquetas reais para impressão
- Envios reais
- Cobranças reais

## 9️⃣ Migrar para Produção

Quando estiver pronto para produção:

1. **Obtenha Production API Key:**
   - Dashboard → API Keys → Production
   - Começa com `EZAK...`

2. **Adicione método de pagamento:**
   - Dashboard → Billing
   - Adicione cartão de crédito

3. **Atualize .env:**
```bash
EASYPOST_API_KEY=EZAK_sua_production_key
APP_ENV=production
APP_DEBUG=false
```

4. **Custos:**
   - Varia por carrier e serviço
   - USPS First Class: ~$3-$8
   - USPS Priority: ~$8-$15
   - + Taxa EasyPost: $0.05 por label

## 🔟 Recursos Úteis

- 📚 **Documentação EasyPost:** https://docs.easypost.com/
- 🧪 **Test Mode:** https://docs.easypost.com/docs/test-mode
- 💰 **Pricing:** https://www.easypost.com/pricing
- 📞 **Suporte:** support@easypost.com

## ✅ Checklist de Configuração

- [ ] Conta EasyPost criada
- [ ] Test API Key copiada
- [ ] `EASYPOST_API_KEY` no `.env`
- [ ] Teste de criação de label funcionando
- [ ] Logs sem erros

---

**Pronto!** Sua aplicação está configurada para criar etiquetas de envio! 📦🎉


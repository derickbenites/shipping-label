=====================================
📮 POSTMAN COLLECTION - PRONTO!
=====================================

✅ Collection do Postman criada com sucesso!

📦 ARQUIVOS CRIADOS:
=====================================

1. Shipping_Label_API.postman_collection.json
   → Collection completa com 12 requests
   → Scripts automáticos
   → Exemplos prontos para usar

2. Shipping_Label_API.postman_environment.json
   → Environment com variáveis configuradas
   → base_url, token, label_id

3. POSTMAN_GUIDE.md
   → Guia completo de uso
   → Passo a passo
   → Troubleshooting

=====================================
🚀 COMO USAR:
=====================================

PASSO 1: Importar no Postman
-----------------------------
1. Abra o Postman
2. Clique em "Import"
3. Arraste estes 2 arquivos:
   - Shipping_Label_API.postman_collection.json
   - Shipping_Label_API.postman_environment.json
4. Clique em "Import"

PASSO 2: Selecionar Environment
-----------------------------
1. Canto superior direito do Postman
2. Dropdown de environments
3. Selecione "Shipping Label API - Local"

PASSO 3: Começar a Testar
-----------------------------
1. Vá em "Authentication" → "Register"
2. Clique em "Send"
3. Token será salvo automaticamente!
4. Agora use as outras requests

=====================================
📋 O QUE TEM NA COLLECTION:
=====================================

AUTHENTICATION (4 requests):
✓ Register - Criar conta
✓ Login - Fazer login
✓ Get Current User - Dados do usuário
✓ Logout - Sair

SHIPPING LABELS (6 requests):
✓ List All Labels - Listar todas
✓ Create Label - Criar etiqueta completa
✓ Create Label - Simple - Criar simples
✓ Get Label Details - Ver detalhes
✓ Get Rates - Consultar tarifas
✓ Cancel/Refund Label - Cancelar

EXAMPLES (2 requests):
✓ EasyPost Test Address - Endereços oficiais
✓ LA to NYC - Exemplo prático

Total: 12 REQUESTS PRONTAS!

=====================================
✨ RECURSOS AUTOMÁTICOS:
=====================================

✅ Token salvo automaticamente após login
✅ Label ID salvo após criar etiqueta
✅ Variáveis preenchidas automaticamente
✅ Scripts de teste automáticos
✅ Console logs informativos
✅ Documentação em cada request

=====================================
🔧 VARIÁVEIS AUTOMÁTICAS:
=====================================

{{base_url}}
  → http://localhost:8000/api
  → Mude se sua API estiver em outra porta

{{token}}
  → Preenchido automaticamente após login
  → Usado em todas as requests protegidas

{{label_id}}
  → Preenchido automaticamente ao criar label
  → Usado para ver/cancelar label específica

{{user_email}}
  → Email do usuário registrado
  → Salvo automaticamente

=====================================
🎯 FLUXO RECOMENDADO:
=====================================

1. Register
   ↓ (token automático)

2. Get Current User
   ↓ (confirma login)

3. Get Rates
   ↓ (vê tarifas)

4. Create Label
   ↓ (label_id automático)

5. List All Labels
   ↓ (vê todas)

6. Get Label Details
   ↓ (usa {{label_id}})

7. Cancel Label
   ↓ (cancela se quiser)

8. Logout

=====================================
📝 EXEMPLOS DE USO:
=====================================

CRIAR ETIQUETA SIMPLES:
{
  "from_name": "Sender",
  "from_street1": "123 Main St",
  "from_city": "Los Angeles",
  "from_state": "CA",
  "from_zip": "90001",
  "to_name": "Recipient",
  "to_street1": "456 Broadway",
  "to_city": "New York",
  "to_state": "NY",
  "to_zip": "10001",
  "weight": 16
}

CRIAR ETIQUETA COMPLETA:
{
  "from_name": "John's Store",
  "from_company": "John Store Inc",
  "from_street1": "417 Montgomery St",
  "from_street2": "Floor 5",
  "from_city": "San Francisco",
  "from_state": "CA",
  "from_zip": "94104",
  "from_phone": "415-123-4567",
  "to_name": "Jane Smith",
  "to_company": "Smith Corp",
  "to_street1": "1 E 161 St",
  "to_street2": "Apt 5B",
  "to_city": "The Bronx",
  "to_state": "NY",
  "to_zip": "10451",
  "to_phone": "917-555-1234",
  "weight": 16,
  "length": 12,
  "width": 8,
  "height": 6
}

=====================================
⚡ DICAS RÁPIDAS:
=====================================

✓ Sempre selecione o environment
✓ Faça login antes de usar outras requests
✓ Veja o console do Postman (logs úteis)
✓ Use "Save as Example" para salvar responses
✓ Duplique requests para criar variações
✓ Scripts de test já estão prontos
✓ Todas as requests têm documentação

=====================================
🔍 TROUBLESHOOTING:
=====================================

Erro 401 (Não Autenticado):
→ Faça login novamente
→ Verifique se environment está selecionado
→ Token deve estar em {{token}}

Erro 422 (Validação):
→ State: 2 letras (CA, NY)
→ ZIP: 12345 ou 12345-6789
→ Peso em onças (16 oz = 1 lb)
→ Apenas endereços dos EUA

Erro 500 (Servidor):
→ Verifique EASYPOST_API_KEY no .env
→ API deve estar rodando
→ Use endereços de teste

Variáveis não salvam:
→ Selecione environment correto
→ Veja canto superior direito
→ "Shipping Label API - Local"

=====================================
📚 DOCUMENTAÇÃO COMPLETA:
=====================================

Para mais detalhes, leia:

1. POSTMAN_GUIDE.md
   → Guia completo com imagens
   → Passo a passo detalhado
   → Todos os recursos

2. API_DOCUMENTATION.md
   → Documentação da API
   → Todos os endpoints
   → Exemplos com cURL

3. SETUP_EASYPOST.md
   → Configurar EasyPost
   → Obter API Key
   → Endereços de teste

=====================================
✅ CHECKLIST:
=====================================

Antes de usar:
□ API rodando (docker-compose ps)
□ EASYPOST_API_KEY configurado
□ Collection importada
□ Environment importado
□ Environment selecionado

Pronto para testar!

=====================================
🎉 TUDO PRONTO!
=====================================

Você tem agora:

✅ Collection completa (12 requests)
✅ Environment configurado
✅ Variáveis automáticas
✅ Scripts de teste
✅ Documentação completa
✅ Exemplos prontos

IMPORTE E USE! 🚀📮

=====================================

Para começar:
1. Importe os 2 arquivos JSON no Postman
2. Selecione o environment
3. Execute "Register"
4. Comece a criar etiquetas!

Leia POSTMAN_GUIDE.md para detalhes.

=====================================


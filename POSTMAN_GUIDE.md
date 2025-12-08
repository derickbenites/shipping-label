# 📮 Guia de Uso do Postman

Collection completa para testar a API de Shipping Labels.

---

## 🚀 Importar no Postman

### Método 1: Importar Collection + Environment (Recomendado)

1. **Abra o Postman**

2. **Importe a Collection:**
   - Clique em `Import` no canto superior esquerdo
   - Arraste o arquivo `Shipping_Label_API.postman_collection.json`
   - Ou clique em `Upload Files` e selecione o arquivo
   - Clique em `Import`

3. **Importe o Environment:**
   - Clique em `Import` novamente
   - Arraste o arquivo `Shipping_Label_API.postman_environment.json`
   - Clique em `Import`

4. **Selecione o Environment:**
   - No canto superior direito, clique no dropdown de environments
   - Selecione `Shipping Label API - Local`

### Método 2: Importar apenas Collection (variáveis na collection)

Se você só importar a collection, as variáveis já estão configuradas dentro dela.

---

## 📋 Variáveis de Ambiente

Após importar, você terá estas variáveis:

| Variável | Valor Padrão | Descrição |
|----------|--------------|-----------|
| `base_url` | `http://localhost:8000/api` | URL base da API |
| `token` | (auto) | Token de autenticação (preenchido automaticamente) |
| `label_id` | (auto) | ID da última label criada (preenchido automaticamente) |
| `user_email` | (auto) | Email do usuário registrado (preenchido automaticamente) |

**Nota:** As variáveis `token` e `label_id` são preenchidas automaticamente após você fazer login ou criar uma label.

---

## 🎯 Como Usar - Passo a Passo

### 1️⃣ Configurar Ambiente (Primeira Vez)

Se necessário, ajuste a `base_url`:
- Clique no ícone de "olho" 👁️ no canto superior direito
- Clique em `Edit` ao lado do environment
- Modifique `base_url` se sua API não estiver em `localhost:8000`

### 2️⃣ Registrar ou Fazer Login

**Opção A: Registrar novo usuário**
1. Vá em `Authentication` → `Register`
2. Modifique o email no body se quiser
3. Clique em `Send`
4. ✅ O token será salvo automaticamente na variável `{{token}}`

**Opção B: Fazer login com usuário existente**
1. Vá em `Authentication` → `Login`
2. Ajuste email e senha no body
3. Clique em `Send`
4. ✅ O token será salvo automaticamente

### 3️⃣ Criar uma Etiqueta

1. Vá em `Shipping Labels` → `Create Label`
2. Revise os dados no body (endereços, peso, etc)
3. Clique em `Send`
4. ✅ O ID da label será salvo automaticamente em `{{label_id}}`
5. 📄 Veja o `label_pdf_url` no response para imprimir

### 4️⃣ Listar Etiquetas

1. Vá em `Shipping Labels` → `List All Labels`
2. Clique em `Send`
3. ✅ Veja todas as suas etiquetas

### 5️⃣ Ver Detalhes de uma Etiqueta

1. Vá em `Shipping Labels` → `Get Label Details`
2. A URL já usa `{{label_id}}` automaticamente
3. Clique em `Send`
4. ✅ Veja os detalhes completos

### 6️⃣ Consultar Tarifas (Sem Comprar)

1. Vá em `Shipping Labels` → `Get Rates (No Purchase)`
2. Ajuste os endereços se quiser
3. Clique em `Send`
4. ✅ Veja as tarifas USPS disponíveis

---

## 📦 Folders na Collection

### 1. **Authentication** (4 endpoints)
```
- Register          → Criar nova conta
- Login             → Fazer login
- Get Current User  → Ver dados do usuário
- Logout            → Fazer logout
```

### 2. **Shipping Labels** (6 endpoints)
```
- List All Labels           → Listar etiquetas (paginado)
- Create Label              → Criar nova etiqueta (completo)
- Create Label - Simple     → Criar etiqueta (campos mínimos)
- Get Label Details         → Ver etiqueta específica
- Get Rates (No Purchase)   → Consultar tarifas
- Cancel/Refund Label       → Cancelar/Reembolsar
```

### 3. **Examples - Test Addresses** (2 exemplos)
```
- Create Label - EasyPost Test Address  → Endereços oficiais de teste
- Create Label - LA to NYC              → Exemplo LA → NY
```

---

## 🔧 Recursos Automáticos

### Scripts de Test (Auto-executados)

Todas as requisições principais têm scripts que executam automaticamente:

1. **Register/Login:**
   - Salva o token em `{{token}}`
   - Salva o email em `{{user_email}}`

2. **Create Label:**
   - Salva o ID da label em `{{label_id}}`
   - Mostra no console: ID, tracking code, rate, PDF URL

3. **Get Rates:**
   - Mostra informações no console

### Console do Postman

Abra o console (View → Show Postman Console) para ver:
- ✅ Token saved: ...
- ✅ Label created with ID: 1
- ✅ Tracking code: 9400...
- ✅ Rate: $7.33
- ✅ PDF URL: https://...

---

## 📝 Exemplos de Body

### Criar Etiqueta - Completo
```json
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
```

### Criar Etiqueta - Simples (Mínimo)
```json
{
  "from_name": "Sender Name",
  "from_street1": "123 Main St",
  "from_city": "Los Angeles",
  "from_state": "CA",
  "from_zip": "90001",
  
  "to_name": "Recipient Name",
  "to_street1": "456 Broadway",
  "to_city": "New York",
  "to_state": "NY",
  "to_zip": "10001",
  
  "weight": 16
}
```

---

## ⚠️ Importante

### Antes de Começar

1. ✅ **Certifique-se que a API está rodando:**
   ```bash
   docker-compose ps
   # Deve mostrar containers rodando
   ```

2. ✅ **Configure a chave EasyPost no `.env`:**
   ```bash
   EASYPOST_API_KEY=EZTK_sua_chave_de_teste
   ```

3. ✅ **Selecione o environment correto no Postman:**
   - Canto superior direito → `Shipping Label API - Local`

### Validações

- ✅ Apenas endereços dos **Estados Unidos**
- ✅ State: **2 letras** (CA, NY, TX, etc)
- ✅ ZIP: formato **12345** ou **12345-6789**
- ✅ Weight: em **onças** (16 oz = 1 lb)
- ✅ Dimensões: em **polegadas** (opcional)
- ✅ Peso máximo: **1120 oz** (70 lbs)

---

## 🧪 Fluxo de Teste Completo

Execute nesta ordem:

```
1. Register
   ↓ (token salvo automaticamente)
   
2. Get Current User
   ↓ (confirma autenticação)
   
3. Get Rates (No Purchase)
   ↓ (vê tarifas disponíveis)
   
4. Create Label
   ↓ (label_id salvo automaticamente)
   
5. List All Labels
   ↓ (vê todas as labels)
   
6. Get Label Details
   ↓ (usa {{label_id}} automático)
   
7. Cancel/Refund Label (opcional)
   ↓ (cancela a label criada)
   
8. Logout
```

---

## 🎨 Dicas do Postman

### Visualizar Response Bonito
- Clique na aba `Pretty` no response
- Use `JSON` para formatar

### Salvar Exemplos
- Após uma request bem-sucedida
- Clique em `Save Response` → `Save as example`

### Duplicar Requests
- Clique com botão direito na request
- Selecione `Duplicate`
- Modifique para criar variações

### Organizar
- Crie novos folders arrastando requests
- Renomeie requests para identificar facilmente

### Testes Automatizados
- Vá na aba `Tests` de cada request
- Os scripts já estão prontos
- Você pode adicionar mais testes

---

## 🔍 Troubleshooting

### Erro 401 (Não Autenticado)
**Causa:** Token ausente ou inválido

**Solução:**
1. Faça login novamente
2. Verifique se o environment está selecionado
3. Verifique se `{{token}}` está no header Authorization

### Erro 422 (Validação)
**Causa:** Dados inválidos no body

**Solução:**
1. Verifique os campos obrigatórios
2. Confirme formato de state (2 letras)
3. Confirme formato de ZIP (12345 ou 12345-6789)
4. Peso em onças

### Erro 500 (EasyPost)
**Causa:** Problema com API EasyPost

**Solução:**
1. Verifique se `EASYPOST_API_KEY` está configurado
2. Use endereços de teste oficiais
3. Veja logs: `docker-compose logs -f app`

### Variáveis não salvam
**Causa:** Environment não selecionado

**Solução:**
1. Verifique environment no canto superior direito
2. Selecione `Shipping Label API - Local`
3. Tente login novamente

---

## 📚 Documentação Adicional

Para mais informações, consulte:

- 📖 **API_DOCUMENTATION.md** - Documentação completa da API
- 🔧 **SETUP_EASYPOST.md** - Configurar EasyPost
- ✨ **FEATURES.md** - Funcionalidades implementadas
- 📝 **RESUMO_IMPLEMENTACAO.md** - Resumo técnico

---

## 🎉 Pronto para Usar!

Sua collection do Postman está completa com:

✅ 12 requests prontas para usar
✅ Variáveis automáticas (token, label_id)
✅ Scripts de test automáticos
✅ Exemplos com endereços de teste
✅ Documentação em cada request
✅ Console logs informativos

**Importe e comece a testar!** 🚀

---

**Desenvolvido para facilitar testes da API Shipping Label**


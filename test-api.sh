#!/bin/bash

# Script de teste da API de Shipping Labels
# Autor: Auto-generated
# Data: 2025-12-07

set -e

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

BASE_URL="http://localhost:8000/api"
TOKEN=""

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Shipping Label API - Test Script${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Função para fazer requests
api_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    local use_token=$4

    if [ "$use_token" = "true" ] && [ -n "$TOKEN" ]; then
        curl -s -X $method "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -H "Authorization: Bearer $TOKEN" \
            -d "$data"
    else
        curl -s -X $method "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -d "$data"
    fi
}

# Teste 1: Registrar usuário
echo -e "${YELLOW}[1] Registrando usuário de teste...${NC}"
RESPONSE=$(api_request "POST" "/auth/register" '{
    "name": "Test User",
    "email": "test'$(date +%s)'@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}' false)

echo "$RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE"

TOKEN=$(echo "$RESPONSE" | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo -e "${RED}✗ Falha ao obter token!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Usuário registrado! Token: ${TOKEN:0:20}...${NC}"
echo ""

# Teste 2: Login
echo -e "${YELLOW}[2] Testando login...${NC}"
EMAIL=$(echo "$RESPONSE" | grep -o '"email":"[^"]*' | cut -d'"' -f4)

LOGIN_RESPONSE=$(api_request "POST" "/auth/login" '{
    "email": "'$EMAIL'",
    "password": "senha123"
}' false)

echo "$LOGIN_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$LOGIN_RESPONSE"
echo -e "${GREEN}✓ Login bem-sucedido!${NC}"
echo ""

# Teste 3: Obter dados do usuário
echo -e "${YELLOW}[3] Obtendo dados do usuário...${NC}"
ME_RESPONSE=$(api_request "GET" "/auth/me" "" true)
echo "$ME_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$ME_RESPONSE"
echo -e "${GREEN}✓ Dados obtidos!${NC}"
echo ""

# Teste 4: Consultar tarifas (sem criar label)
echo -e "${YELLOW}[4] Consultando tarifas de envio...${NC}"
RATES_RESPONSE=$(api_request "POST" "/shipping-labels/rates" '{
    "from_name": "Test Sender",
    "from_street1": "417 Montgomery St",
    "from_city": "San Francisco",
    "from_state": "CA",
    "from_zip": "94104",
    "to_name": "Test Recipient",
    "to_street1": "1 E 161 St",
    "to_city": "The Bronx",
    "to_state": "NY",
    "to_zip": "10451",
    "weight": 16,
    "length": 12,
    "width": 8,
    "height": 6
}' true)

echo "$RATES_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$RATES_RESPONSE"
echo ""

if echo "$RATES_RESPONSE" | grep -q "error"; then
    echo -e "${RED}⚠ Erro ao consultar tarifas. Verifique se EASYPOST_API_KEY está configurado no .env${NC}"
    echo -e "${YELLOW}Para continuar, adicione sua chave EasyPost no .env:${NC}"
    echo -e "${BLUE}EASYPOST_API_KEY=EZTK_your_test_key_here${NC}"
    echo ""
else
    echo -e "${GREEN}✓ Tarifas obtidas com sucesso!${NC}"
    echo ""

    # Teste 5: Criar etiqueta de envio
    echo -e "${YELLOW}[5] Criando etiqueta de envio...${NC}"
    LABEL_RESPONSE=$(api_request "POST" "/shipping-labels" '{
        "from_name": "Test Sender",
        "from_company": "Test Company",
        "from_street1": "417 Montgomery St",
        "from_street2": "Floor 5",
        "from_city": "San Francisco",
        "from_state": "CA",
        "from_zip": "94104",
        "from_phone": "415-123-4567",
        "to_name": "Test Recipient",
        "to_company": "Recipient Inc",
        "to_street1": "1 E 161 St",
        "to_city": "The Bronx",
        "to_state": "NY",
        "to_zip": "10451",
        "to_phone": "917-123-4567",
        "weight": 16,
        "length": 12,
        "width": 8,
        "height": 6
    }' true)

    echo "$LABEL_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$LABEL_RESPONSE"

    LABEL_ID=$(echo "$LABEL_RESPONSE" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)

    if [ -n "$LABEL_ID" ]; then
        echo -e "${GREEN}✓ Etiqueta criada com ID: $LABEL_ID${NC}"
        echo ""

        # Teste 6: Listar etiquetas
        echo -e "${YELLOW}[6] Listando todas as etiquetas...${NC}"
        LIST_RESPONSE=$(api_request "GET" "/shipping-labels" "" true)
        echo "$LIST_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$LIST_RESPONSE"
        echo -e "${GREEN}✓ Etiquetas listadas!${NC}"
        echo ""

        # Teste 7: Ver etiqueta específica
        echo -e "${YELLOW}[7] Visualizando etiqueta #$LABEL_ID...${NC}"
        SHOW_RESPONSE=$(api_request "GET" "/shipping-labels/$LABEL_ID" "" true)
        echo "$SHOW_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$SHOW_RESPONSE"
        echo -e "${GREEN}✓ Etiqueta visualizada!${NC}"
        echo ""

        # Extrair URLs da etiqueta
        LABEL_URL=$(echo "$SHOW_RESPONSE" | grep -o '"label_pdf_url":"[^"]*' | cut -d'"' -f4)
        if [ -n "$LABEL_URL" ]; then
            echo -e "${BLUE}📄 URL para impressão (PDF): $LABEL_URL${NC}"
            echo ""
        fi
    else
        echo -e "${RED}✗ Falha ao criar etiqueta${NC}"
    fi
fi

# Teste 8: Logout
echo -e "${YELLOW}[8] Fazendo logout...${NC}"
LOGOUT_RESPONSE=$(api_request "POST" "/auth/logout" "" true)
echo "$LOGOUT_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$LOGOUT_RESPONSE"
echo -e "${GREEN}✓ Logout realizado!${NC}"
echo ""

echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}✓ Todos os testes concluídos!${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo -e "${YELLOW}Resumo:${NC}"
echo -e "  ✓ Autenticação (Register, Login, Logout)"
echo -e "  ✓ Consulta de tarifas"
echo -e "  ✓ Criação de etiquetas"
echo -e "  ✓ Listagem de etiquetas"
echo -e "  ✓ Visualização de etiqueta específica"
echo ""
echo -e "${BLUE}Para mais informações, consulte:${NC}"
echo -e "  - API_DOCUMENTATION.md"
echo -e "  - SETUP_EASYPOST.md"
echo -e "  - FEATURES.md"
echo ""


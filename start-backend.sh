#!/bin/bash

# Define the container names
DB_CONTAINER_NAME="datum-teste_db_1"  # Update this if necessary
APP_CONTAINER_NAME="backend-api_app"

# Definindo a cor verde
GREEN="\e[32m"
# Resetando para a cor padrão
RESET="\e[0m"

echo -e "${GREEN}Verificando se o container de banco de dados está em execução...${RESET}"
# Verify that the database container is running
if ! docker ps --format '{{.Names}}' | grep -q "^$DB_CONTAINER_NAME$"; then
  echo -e "${GREEN}Erro: O container $DB_CONTAINER_NAME não está em execução.${RESET}"
  exit 1
fi

echo -e "${GREEN}Container de banco de dados está em execução.${RESET}"

echo -e "${GREEN}Copiando .env.example para .env dentro do container da aplicação...${RESET}"
# Copiar .env.example para .env no contêiner
docker exec -i "$APP_CONTAINER_NAME" sh -c "cp .env.example .env"

echo -e "${GREEN}Verificando/criando o banco de dados...${RESET}"
CREATE_DB_COMMAND="CREATE DATABASE IF NOT EXISTS banking;"

# Execute the command in the MySQL container (suppress the password warning)
docker exec -i "$DB_CONTAINER_NAME" mysql -uroot -proot -e "$CREATE_DB_COMMAND" 2>/dev/null

echo -e "${GREEN}Banco de dados verificado/criado.${RESET}"

echo -e "${GREEN}Instalando Laravel Sanctum via Composer...${RESET}"
# Instalar Laravel Sanctum via Composer
docker exec -i "$APP_CONTAINER_NAME" sh -c "composer require laravel/sanctum"

echo -e "${GREEN}Publicando arquivos de configuração do Sanctum...${RESET}"
# Publicar arquivos de configuração do Sanctum
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan vendor:publish --provider=\"Laravel\\Sanctum\\SanctumServiceProvider\""

echo -e "${GREEN}Verificando conexão com o banco de dados...${RESET}"
# Check if using SQLite and handle migrations accordingly
DB_CONNECTION=$(docker exec -i "$APP_CONTAINER_NAME" sh -c "php -r 'echo config(\"database.default\");'")

if [ "$DB_CONNECTION" = "sqlite" ]; then
  echo -e "${GREEN}Usando banco de dados SQLite. Executando migrações...${RESET}"
  # Run the migrations without rollback, as it can fail if tables don't exist
  docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan migrate:fresh --force"
else
  echo -e "${GREEN}Usando banco de dados não-SQLite. Executando rollback e migrações...${RESET}"
  # For non-SQLite databases, allow rollback and migration
  docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan migrate:fresh --force"
fi

echo -e "${GREEN}Executando outros comandos de manutenção...${RESET}"
# Outros comandos de manutenção
docker exec -i "$APP_CONTAINER_NAME" sh -c "composer dump-autoload"
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan config:clear"
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan cache:clear"
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan route:clear"

echo -e "${GREEN}Instalando dependências com Composer...${RESET}"
docker exec -i "$APP_CONTAINER_NAME" sh -c "composer install --ignore-platform-reqs"

echo -e "${GREEN}Gerando chave da aplicação...${RESET}"
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan key:generate"

echo -e "${GREEN}Configurando permissões no diretório storage...${RESET}"
# Configurar permissões
docker exec -i "$APP_CONTAINER_NAME" sh -c "chmod -R 775 storage/app/public"

echo -e "${GREEN}Executando Seeders...${RESET}"
# Executar Seeders
docker exec -i "$APP_CONTAINER_NAME" sh -c "php artisan db:seed"

echo -e "${GREEN}Script concluído com sucesso!${RESET}"

# Exibindo a imagem do elefante com um joinha e a frase de confirmação em verde
echo -e "${GREEN}
┈┈┈┈┈┈▕▔╲        ╰╮╰╮╰╮
┈┈┈┈┈┈┈▏▕       ╭━━━━━━━╮╱ 
┈┈┈┈┈┈┈▏▕▂▂▂    ╰━━━━━━━╯╱ 
 ▂▂▂▂▂▂╱┈▕▂▂▂▏  ┃╭╭╮┏┏┏┏┣━╮
 ▉▉▉▉▉┈┈┈▕▂▂▂▏  ┃┃┃┃┣┣┣┣┃╱┃ 
 ▉▉▉▉▉┈┈┈▕▂▂▂▏  ┃╰╰╯┃┃┗┗┣━╯ 
 ▔▔▔▔▔▔╲▂▕▂▂▂▏  ╰━━━━━━━╯ 

Servidor - OK
${RESET}"

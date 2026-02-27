cat << 'EOF' > README.md
## Installation & Setup

### 1. Clone the Repository
git clone https://github.com/xiverisx/corepetitustask.git .

### 2. Environment Configuration
cp .env.example .env
cp .env.example src/.env

### 3. Start Docker Containers
docker-compose up -d --build

### 4. Install Dependencies
docker exec corepetitustask-app composer install

### 5. Database Setup
docker exec corepetitustask-app php bin/console doctrine:database:create --if-not-exists
docker exec corepetitustask-app php bin/console doctrine:migrations:migrate --no-interaction
docker exec corepetitustask-app php bin/console doctrine:fixtures:load --no-interaction

### 6. App endpoints
- Menu Navigation: http://localhost:8080/
- People Management: http://localhost:8080/people
EOF
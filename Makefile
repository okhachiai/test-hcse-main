DOCKER_COMPOSE := docker compose
APP_SERVICE := app
DB_SERVICE := db

.PHONY: help build up down restart ps logs clean \
	init install deps keygen migrate seed fresh storage-link \
	assets-build assets-dev test lint analyse phpstan pint pint-fix rector-check rector quality wait-db \
	openapi-docs api-docs shell db-shell artisan composer npm

help:
	@echo "Commandes disponibles:"
	@echo "  make build         - Build les images Docker"
	@echo "  make up            - Démarre les conteneurs en arrière-plan"
	@echo "  make down          - Arrête les conteneurs"
	@echo "  make restart       - Redémarre les conteneurs"
	@echo "  make ps            - Affiche l'état des services"
	@echo "  make logs          - Suit les logs des services"
	@echo "  make clean         - Arrête et supprime les volumes (reset DB)"
	@echo "  make init          - Setup complet (env, build, up, wait-db, deps, key, migrate, assets)"
	@echo "  make install       - Installe dépendances PHP + Node"
	@echo "  make migrate       - Lance les migrations"
	@echo "  make seed          - Lance les seeders"
	@echo "  make fresh         - Reset DB (fresh --seed)"
	@echo "  make storage-link  - Crée le lien storage public"
	@echo "  make assets-build  - Build les assets front"
	@echo "  make assets-dev    - Lance Vite en mode dev"
	@echo "  make test          - Lance les tests Laravel"
	@echo "  make api-docs-link - Affiche l'URL de la doc Swagger"
	@echo "  make openapi-docs  - Génère la doc OpenAPI depuis le code (attributs)"
	@echo "  make lint          - Lance Pint (vérification style)"
	@echo "  make analyse       - Lance Larastan/PHPStan (analyse statique)"
	@echo "  make phpstan       - Alias de make analyse"
	@echo "  make pint          - Lance Pint en dry-run"
	@echo "  make pint-fix      - Lance Pint en mode fix"
	@echo "  make rector	    - Lance Rector en dry-run"
	@echo "  make rector-fix    - Lance Rector en mode fix"
	@echo "  make quality       - Lance lint + analyse + test"
	@echo "  make shell         - Ouvre un shell dans le conteneur app"
	@echo "  make db-shell      - Ouvre un shell MariaDB"
	@echo "  make artisan cmd='route:list'"
	@echo "  make composer cmd='update'"
	@echo "  make npm cmd='run dev'"

build:
	$(DOCKER_COMPOSE) build

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

restart: down up

ps:
	$(DOCKER_COMPOSE) ps

logs:
	$(DOCKER_COMPOSE) logs -f

clean:
	$(DOCKER_COMPOSE) down -v

deps: install

install:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) composer install
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) npm ci

keygen:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan key:generate

migrate:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan migrate

seed:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan db:seed

fresh:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan migrate:fresh --seed

storage-link:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan storage:link --force

assets-build:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) npm run build

assets-dev:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) npm run dev

test:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan test

api-docs-link:
	@echo "Documentation Swagger: http://localhost:8080/api-docs"
	@echo "Spécification OpenAPI: http://localhost:8080/openapi.json"

openapi-docs:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan openapi:generate
	@echo "========================================================="
	$(MAKE) api-docs-link

lint: pint

analyse: phpstan

phpstan:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) ./vendor/bin/phpstan analyse --memory-limit=1G

pint:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) ./vendor/bin/pint --test

pint-fix:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) ./vendor/bin/pint

rector-check:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) ./vendor/bin/rector process --dry-run

rector:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) ./vendor/bin/rector process

quality: lint analyse test

wait-db:
	$(DOCKER_COMPOSE) exec -T $(DB_SERVICE) sh -lc 'until mariadb-admin ping -h localhost -p"$${MYSQL_ROOT_PASSWORD:-root}" --silent; do sleep 2; done'

shell:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) sh

db-shell:
	$(DOCKER_COMPOSE) exec $(DB_SERVICE) mariadb -u$${MYSQL_USER:-hcse} -p$${MYSQL_PASSWORD:-secret} $${MYSQL_DATABASE:-hcse}

artisan:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan $(cmd)

composer:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) composer $(cmd)

npm:
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) npm $(cmd)

init:
	@if [ -f .env.docker.example ]; then cp .env.docker.example .env; \
	elif [ ! -f .env ]; then \
		if [ -f .env.example ]; then cp .env.example .env; \
		else echo "Erreur: aucun fichier .env.example trouvé"; exit 1; \
		fi; \
	fi
	$(DOCKER_COMPOSE) up -d --build
	$(MAKE) wait-db
	$(MAKE) install
	$(MAKE) keygen
	$(DOCKER_COMPOSE) run --rm $(APP_SERVICE) php artisan migrate:fresh --seed
	$(MAKE) storage-link
	$(MAKE) assets-build

## Architecture et décisions techniques

### Structure générale (DDD light)

- **Domain** (`app/Domain/`) : Enums, Value Objects (Sku, Price), Specifications (OfferCanBePublishedSpecification). Code pur, sans dépendance framework.
- **Application** (`app/Application/`) : Actions (use-cases), Data/DTOs, Contracts (interfaces des repositories).
- **Infrastructure** (`app/Infrastructure/`) : Implémentations Eloquent (Repositories), QueryServices (OfferQueryService), ImageStorage.
- **Controllers** : fins, délèguent aux Actions, retournent View/Redirect/JsonResponse.
- **Form Requests** : validation avec Rules (SkuRule, PriceRule, OfferCanBePublishedRule), `Rule::enum()` pour les états.

### Enums

- **OfferState** / **ProductState** : backed enums (`draft`, `published`, `hidden`/`invisible`) avec `label()` et `labels()` pour l'UI. Remplacement des tableaux statiques côté modèles.
- **Pagination** : valeurs par défaut (perPage). Utile pour l'API et la pagination.

### ImageStorage

- Service dédié au stockage des images : `store()`, `replace()`, `delete()`.
- Utilise le disque `public`, génère des UUID pour éviter les collisions.
- Gère le remplacement (nouveau fichier + suppression de l'ancien) et les placeholders.

### API Resources

- **OfferResource** / **ProductResource** : sérialisation des réponses API, exclusion des champs internes pour une structure de réponse stable.

### DTO / Data

- **DashboardData** : objet readonly pour les données du dashboard (offers, filterParams, activeState, offerStates). Évite de passer des tableaux dans les vues.

### Scopes et filtres

- `published()`, `draft()`, `ofState()` : scopes Eloquent pour les états.
- Dashboard : filtres par state, name, slug avec `withQueryString()` pour conserver les paramètres en pagination.

---

## Ce qui a été modifié (résumé)

- **Enums** : états typés (OfferState, ProductState) avec `allowedTransitions()` et `canTransitionTo()` pour les règles de transition.
- **Actions** : logique métier extraite (Create, Update, Delete, List). Controllers allégés.
- **Repositories** : requêtes encapsulées, scopes `published()`/`draft()`, tri `latest()` par défaut.
- **ImageStorage** : service dédié pour stocker et remplacer les images.
- **Resources** : réponses API normalisées via OfferResource/ProductResource.
- **Dashboard** : filtres (state, name, slug) + pagination avec préservation des query strings.
- **Validation** : FormRequests avec `Rule::enum()`, règles partagées.
- **Tests** : unitaires (scopes, repositories, ImageStorage, OfferState, ProductState, Sku, Price, OfferCanBePublishedSpecification, OfferQueryService), feature (API, dashboard, validation, console).
- **PHPStan** : niveau 9, sans baseline, erreurs corrigées.
- **CI** : GitHub Actions (lint Pint, PHPStan, tests) sur push/PR vers `main`.
- **Bonus** : API rate limiting (60 req/min), ProductPolicy, règles de transition d’état dans les Enums.
- **DDD light** : Domain (Enums, Sku, Price VOs, OfferCanBePublishedSpecification), Application (Actions, Contracts), Infrastructure (Repositories, OfferQueryService). Interfaces Repository + bindings.

---

## Temps passé et pistes d'amélioration

**Temps passé : 4h**

**Avec plus de temps, j'aurais :**
- Introduire des jobs asynchrones pour le traitement des images (resize, optimisation)
- Introduire un system de cache robuste pour améliorer les perfs
- Ajouter des tests E2E (Dusk) pour les flux critiques du back-office

## Environnement et installation

### Prérequis
- PHP 8.5+
- Composer 2
- Node 18+ et npm
- MySQL/MariaDB
- Docker

### Setup complet avec Docker (compose)
| Commande                                                | Description                                    |
|---------------------------------------------------------|------------------------------------------------|
| `git clone git@github.com:okhachiai/test-hcse-main.git` | cloner le projet de github                     |
| `git checkout feat/ddd-bonus-work`                      | Se positioner sur la branch de test            |
| `make init`                                             | Build et lancer le projet en local dans docker |

## Commandes utiles (Makefile, via Docker)

### Qualité de code

| Commande         | Description                                                        |
|------------------|--------------------------------------------------------------------|
| `make pint`      | Pint: vérification du style (PSR-12)                               |
| `make pint-fix`  | Pint: correction automatique du style                              |
| `make analyse`   | PHPStan: analyse statique (niveau 9)                               |
| `make test`      | PHPUnit: lancer tous les tests                                     |
| `make test-unit` | PHPUnit: tests unitaires uniquement                                |
| `make coverage`  | Génère le rapport de couverture HTML (`build/coverage/index.html`) |
| `make quality`   | `pint` + `analyse` + `test`                                        |

### Base de données

| Commande | Description |
|----------|-------------|
| `make fresh` | Reset DB + migrations + seeders |

---

## Liens utiles (app lancée)

| Lien | Description                                |
|------|--------------------------------------------|
| http://localhost:8080 | Page d'accueil                     |
| http://localhost:8080/login | Connexion                                  |
| http://localhost:8080/dashboard | Back-office offres & produits (auth requise) |
| http://localhost:8080/api/offers | API publique, offres et produits publiés   |
| http://localhost:8080/api-docs | Documentation Swagger / OpenAPI            |
| http://localhost:8080/openapi.json | Spécification OpenAPI (JSON)               |

*Avec `php artisan serve`, remplacer le port `8080` par `8000`.*

### Politique de rate limiting (API)

- **`GET /api/offers`** : 60 requêtes/minute par IP.
- Au-delà de la limite : **429 Too Many Requests** avec un message explicite et header `Retry-After`.

---

## Compte de test (Demo User)
 - **Email**: `demo@example.com`
 - **Password**: `demo@example.com`

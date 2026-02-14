# Test technique senior — HelloCSE (Laravel)

Bienvenue ! Ce dépôt sert de base à un test technique destiné à un·e développeur·se senior PHP/Laravel.
Votre mission est d’améliorer techniquement l’application existante autour de la gestion d’offres et de produits.

## Objectif général

- Apporter des améliorations structurelles et de qualité au projet (architecture, tests, qualité de code) tout en conservant le fonctionnement existant.
- L’enjeu est d’évaluer votre capacité à raisonner, structurer, sécuriser et tester un code Laravel dans un contexte proche de la production.

## Contenu actuel du projet (à connaître)
- Back-office simple de gestion d’offres et des produits liés à une offre.
- API publique GET /api/offers retournant uniquement les offres et produits publiés.

## Ce que nous attendons (périmètre minimal)

- Temps indicatif réalisation : 3 à 8 heures.
- Pas de sur-investissement UI/Design. Restez focalisé sur la qualité backend et l’ingénierie.
- Préférez des améliorations progressives et pragmatiques à une réécriture totale.

1) Architecture et séparation des responsabilités
   - Extraire le code métier dans des services/domain pour découpler la couche HTTP de la logique métier.
   - Introduire si nécessaire des classes dédiées (ex: Actions/Services, DTO, Repositories, Query Objects) avec un design clair, testable et documenté.

2) Qualité de code et outillage
   - PHPStan niveau 8 minimum (viser 9 si pertinent) et correction des erreurs remontées.
   - Ajouter/Configurer d’autres outils que vous jugez pertinents (ex: Larastan, PHP-CS-Fixer/Pint, Psalm, Laravel Pint, Rector) avec une configuration minimale et reproductible.
   - Respect des conventions (PSR-12, nommage, règles de complexité raisonnables, petites méthodes, dépendances explicites).

3) Tests
   - Écrire des tests unitaires PHPUnit ciblant la logique métier extraite (services, règles d’état, validations métiers, etc.).
   - Ajouter des tests de feature pertinents (ex: endpoints, règles d’accès, flux critiques).
   - Viser une couverture utile et significative sur les parties clés (pas de « test pour tester »).

4) Données & démos
   - Ajouter des seeders pour fournir un jeu de données de démonstration cohérent (offres + produits, états variés, images simulées si besoin).
   - Veiller à ce que l’appli soit rapidement exploitable après installation (un développeur doit voir une UI et des données en quelques commandes).

5) Robustesse
   - Gestion propre des validations (FormRequest, règles partagées, messages clairs).
   - Gestion des fichiers (images) sécurisée et robuste.
   - Pagination, tri et filtres côté back si nécessaire pour la scalabilité.
   - API Resources/Transformers pour les réponses API (contract stable, filtrage des champs, sérialisation).

6) Documentation
   - Architecture et décisions clés
   - Comment lancer tests et outils
   - Comment naviguer dans le code

## Bonus appréciés (optionnels, choisissez selon le temps / pertinence)

- Patterns avancés (DDD light, Ports/Adapters, Repositories, Query Services, Specification, Value Objects).
- Extraire la logique liée aux états (transitions possibles, règles d’affichage, filtrages par défaut)
- Politique de sécurité (Policies/Gates), middleware d’auth, rate limiting, validation d’input stricte.
- Documentation API (OpenAPI/Swagger), versionnement API, pagination/tri/filtrage RESTful.
- Optimisations perfs (index DB, N+1, caches, Eager Loading par défaut, Scopes).
- CI (GitHub Actions) exécutant lint + static analysis + tests.
- Docker/Sail prêt à l’emploi, Makefile ou scripts pour simplifier les commandes.
- Observers, Events/Listeners, Notifications, Queues (jobs pour traitement d’images par ex.).

## Critères d’évaluation
- Clarté de l’architecture, découpage des responsabilités, lisibilité.
- Qualité des tests (pertinence, couverture utile, isolation, fidélité à la logique métier).
- Niveau de qualité de code (typages, immutabilité quand pertinent, complexité maîtrisée, cohérence globale, commentaires ciblés).
- Robustesse des choix techniques (validation, gestion des états, gestion fichiers, erreurs, sécurité basique).
- Expérience de dev et reproductibilité (setup simple, scripts, doc, seeders, cohérence des environnements).
- Pertinence des bonus si présents (pas nécessaire d’en faire beaucoup; qualité > quantité).

## Consignes de rendu
- Travaillez dans une branche dédiée et ouvrez une Pull Request (ou fournissez un patch) expliquée clairement.
- Commits atomiques et messages explicites.
- Ajoutez/éditez ce README pour décrire vos choix techniques: architecture, services, tests, outillage, limites connues et pistes d’amélioration.
- Si vous ajoutez d’autres outils (Pint, Psalm, Rector…), documentez les commandes dans ce README ou un Makefile.
- Indiquez le temps passé et ce que vous auriez fait avec plus de temps.

## Questions
Si un point n’est pas clair, documentez vos hypothèses directement dans la PR/README et avancez. Vous pouvez proposer des alternatives techniques et expliquer vos arbitrages.

Bon courage et merci !

---

## Architecture et décisions techniques

### Structure générale

- **Controllers** : restent fins, délèguent aux Actions et retournent la réponse (View, Redirect, JsonResponse).
- **Actions** : logique métier atomique (CreateOffer, DeleteProduct, ListDashboardOffers…). Injectées via le container.
- **Repositories** : accès aux données (OfferRepository, ProductRepository). Encapsulent les requêtes et scopes.
- **Form Requests** : validation centralisée (StoreOfferRequest, UpdateProductRequest…) avec `Rule::enum()` pour les états.

### Enums

- **OfferState** / **ProductState** : backed enums (`draft`, `published`, `hidden`/`invisible`) avec `label()` et `labels()` pour l'UI. Remplacement des tableaux statiques côté modèles.
- **Pagination** : valeurs par défaut (perPage). Utile pour l'API et la pagination.

### ImageStorage

- Service dédié au stockage des images : `store()`, `replace()`, `delete()`.
- Utilise le disque `public`, génère des UUID pour éviter les collisions.
- Gère le remplacement (nouveau fichier + suppression de l'ancien) et les placeholders.

### API Resources

- **OfferResource** / **ProductResource** : sérialisation des réponses API, exclusion des champs internes (state, timestamps, offer_id). Structure de réponse stable.

### DTO / Data

- **DashboardData** : objet readonly pour les données du dashboard (offers, filterParams, activeState, offerStates). Évite de passer des tableaux dans les vues.

### Scopes et filtres

- `published()`, `draft()`, `ofState()` : scopes Eloquent pour les états.
- Dashboard : filtres par state, name, slug avec `withQueryString()` pour conserver les paramètres en pagination.

---

## Commandes (Makefile, via Docker)

### Qualité de code

| Commande | Description |
|----------|-------------|
| `make lint` | Pint — vérification du style (PSR-12) |
| `make pint-fix` | Pint — correction automatique du style |
| `make analyse` | PHPStan/Larastan — analyse statique (niveau 5) |
| `make test` | PHPUnit — lancer tous les tests |
| `make test-unit` | PHPUnit — tests unitaires uniquement |
| `make coverage` | Génère le rapport de couverture HTML (`build/coverage/index.html`) |
| `make quality` | `lint` + `analyse` + `test` |

### Base de données

| Commande | Description |
|----------|-------------|
| `make migrate` | Exécute les migrations |
| `make seed` | Exécute les seeders |
| `make fresh` | Reset DB + migrations + seeders |

### Setup complet

| Commande | Description |
|----------|-------------|
| `make init` | `.env`, build, up, wait-db, install, keygen, migrate:fresh --seed, storage-link, assets-build |
| `make install` | `composer install` + `npm ci` |

---

## Ce qui a été modifié (résumé)

- **Enums** : états typés (OfferState, ProductState) à la place de tableaux statiques.
- **Actions** : logique métier extraite (Create, Update, Delete, List). Controllers allégés.
- **Repositories** : requêtes encapsulées, scopes `published()`/`draft()`, tri `latest()` par défaut.
- **ImageStorage** : service dédié pour stocker et remplacer les images.
- **Resources** : réponses API normalisées via OfferResource/ProductResource.
- **Dashboard** : filtres (state, name, slug) + pagination avec préservation des query strings.
- **Validation** : FormRequests avec `Rule::enum()`, règles partagées.
- **Tests** : unitaires (scopes, repositories, ImageStorage), feature (API, dashboard, validation, console).
- **PHPStan** : niveau 9, sans baseline, erreurs corrigées.

---

## Temps passé et pistes d'amélioration

**Temps passé : 6h**

**Avec plus de temps, j'aurais :**
- Mettre en place une CI (GitHub Actions) exécutant lint + analyse + tests
- Augmenter la couverture de tests sur les actions et repositories
- Introduire des jobs asynchrones pour le traitement des images (resize, optimisation)
- Ajouter des tests E2E (Dusk) pour les flux critiques du back-office

## Environnement et installation

### Prérequis
- PHP 8.5+
- Composer 2
- Node 18+ et npm
- MySQL/MariaDB (ou SQLite si vous préférez pour l’exercice)
- Optionnel: Docker + Laravel Sail

### Étapes rapides (local hors Docker)
1. Cloner le repo et installer les dépendances
   - composer install
   - npm ci
2. Copier l’environnement
   - cp .env.example .env
   - Configurer la base de données (DB_*) et le stockage local.
3. Générer la clé d’application
   - php artisan key:generate
4. Exécuter les migrations et seeders
   - php artisan migrate --seed
5. Lier le stockage public
   - php artisan storage:link
6. Builder les assets (si UI utilisée)
   - npm run build (ou npm run dev pour le watch)
7. Lancer l’application
   - php artisan serve (ou via votre stack locale)

### Étapes avec Docker (compose)
1. make init

### Tests et qualité (rappel)

Voir la section **Commandes** ci-dessus pour les détails. Résumé : `make lint`, `make analyse`, `make test`, `make coverage`, `make quality`. Composer : `composer lint`, `composer analyse`, `composer test`. Rector : `make rector`, `make rector-fix`.

---

## Liens utiles (app lancée)

| Lien | Description                                  |
|------|----------------------------------------------|
| http://localhost:8080 | Page d'accueil (Docker)                      |
| http://localhost:8080/login | Connexion                                    |
| http://localhost:8080/dashboard | Back-office offres & produits (auth requise) |
| http://localhost:8080/api/offers | API publique, offres et produits publiés     |
| http://localhost:8080/api-docs | Documentation Swagger / OpenAPI              |
| http://localhost:8080/openapi.json | Spécification OpenAPI (JSON)                 |

*Avec `php artisan serve`, remplacer le port `8080` par `8000`.*

---



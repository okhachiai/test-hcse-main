# Rapport de baseline — HelloCSE Technical Test

## 1. Inventaire des routes

### Routes Web (`routes/web.php`)

| Méthode | URI | Contrôleur | Nom |
|---------|-----|------------|-----|
| GET | `/` | Closure | — |
| GET | `/dashboard` | DashboardController@show | dashboard |
| GET | `/offers/create` | OfferController@create | offers.create |
| POST | `/offers` | OfferController@store | offers.store |
| GET | `/offers/{offerId}` | OfferController@show | offers.show |
| GET | `/offers/{offerId}/edit` | OfferController@edit | offers.edit |
| PATCH | `/offers/{offerId}` | OfferController@update | offers.update |
| DELETE | `/offers/{offerId}` | OfferController@destroy | offers.destroy |
| GET | `/offers/{offerId}/products` | ProductController@index | offers.products.index |
| GET | `/offers/{offerId}/products/create` | ProductController@create | offers.products.create |
| POST | `/offers/{offerId}/products` | ProductController@store | offers.products.store |
| GET | `/offers/{offerId}/products/{productId}/edit` | ProductController@edit | offers.products.edit |
| PATCH | `/offers/{offerId}/products/{productId}` | ProductController@update | offers.products.update |
| DELETE | `/offers/{offerId}/products/{productId}` | ProductController@destroy | offers.products.destroy |
| GET | `/profile` | ProfileController@edit | profile.edit |
| PATCH | `/profile` | ProfileController@update | profile.update |
| DELETE | `/profile` | ProfileController@destroy | profile.destroy |

### Routes Auth (`routes/auth.php`)

| Méthode | URI | Contrôleur |
|---------|-----|------------|
| GET | `/register` | RegisteredUserController@create |
| POST | `/register` | RegisteredUserController@store |
| GET | `/login` | AuthenticatedSessionController@create |
| POST | `/login` | AuthenticatedSessionController@store |
| GET | `/forgot-password` | PasswordResetLinkController@create |
| POST | `/forgot-password` | PasswordResetLinkController@store |
| GET | `/reset-password/{token}` | NewPasswordController@create |
| POST | `/reset-password` | NewPasswordController@store |
| GET | `/verify-email` | EmailVerificationPromptController |
| GET | `/verify-email/{id}/{hash}` | VerifyEmailController |
| POST | `/email/verification-notification` | EmailVerificationNotificationController@store |
| GET | `/confirm-password` | ConfirmablePasswordController@show |
| POST | `/confirm-password` | ConfirmablePasswordController@store |
| PUT | `/password` | PasswordController@update |
| POST | `/logout` | AuthenticatedSessionController@destroy |

### Routes API (`routes/api.php`) — préfixe `/api`

| Méthode | URI | Contrôleur | Auth |
|---------|-----|------------|------|
| GET | `/api/user` | Closure | auth:sanctum |
| GET | `/api/offers` | Api\OfferController@index | Public |

---

## 2. Contrôleurs impliqués

| Contrôleur | Rôle |
|------------|------|
| **DashboardController** | Liste des offres avec filtres (state, name, slug) |
| **OfferController** | CRUD offres (web) |
| **ProductController** | CRUD produits imbriqués dans les offres |
| **Api\OfferController** | Liste des offres publiées (API JSON) |
| **ProfileController** | Profil utilisateur (edit, update, destroy) |
| **Auth\*** | Authentification Breeze (login, register, password, email verification) |

---

## 3. Validation, uploads et réponses API

### Validation

| Emplacement | Type | Règles |
|-------------|------|--------|
| **ProfileUpdateRequest** | FormRequest | name, email (unique) |
| **Auth\LoginRequest** | FormRequest | email, password |
| **OfferController** | `$request->validate()` inline | store: name, slug, image, description, state — update: idem + image required |
| **ProductController** | `$request->validate()` inline | name, sku (unique), image, price, state |
| **ProfileController@destroy** | `validateWithBag()` | password (current_password) |
| **Auth\*** | `$request->validate()` inline | selon chaque contrôleur |

### Uploads de fichiers

| Contrôleur | Champ | Disque | Chemin |
|------------|-------|--------|--------|
| **OfferController@store** | `image` | public | `offers/` |
| **OfferController@update** | `image` | public | `offers/` |
| **ProductController@store** | `image` | public | `products/` |
| **ProductController@update** | `image` (nullable) | public | `products/` |

### Réponses API

| Endpoint | Format | Contrôleur |
|----------|--------|------------|
| `GET /api/user` | JSON (user) | Closure |
| `GET /api/offers` | `response()->json($offers)` | Api\OfferController |

Pas de Resource/Transformer : les modèles Eloquent sont renvoyés tels quels (dates, relations, etc.).

---

## 4. Risques N+1

| Emplacement | Risque | Détail |
|-------------|--------|--------|
| **DashboardController** | Faible | Pas d'accès aux produits dans la vue dashboard. |
| **OfferController@show** | OK | `Offer::with('products')->findOrFail()` — eager loading. |
| **ProductController@index** | OK | `$offer->products()` — une seule requête. |
| **Api\OfferController@index** | OK | `with('products', ...)` — eager loading (mais syntaxe incorrecte, voir ci-dessous). |

### Problème dans Api\OfferController

```php
Offer::ofState('published')->with('products', fn ($q) => $q->where('state', 'published'))->get();
```

La syntaxe correcte pour le constrained eager loading est :

```php
with(['products' => fn ($q) => $q->where('state', 'published')])
```

Avec `with('products', fn...)`, le second argument est ignoré, donc la contrainte sur les produits n'est pas appliquée.

---

## 5. Autres points d'attention

- **OfferController@edit** : `Offer::find($offerId)` — pas de `findOrFail`, erreur 500 si offre inexistante.
- **OfferController@update** : `Offer::find($offerId)` appelé deux fois — à factoriser.
- **OfferController@update** : `image` requis à chaque mise à jour — oblige à re-uploader l'image même sans changement.
- **ProductController@store** : `$product->update(['image' => ...])` après `save()` — l'image pourrait être gérée dans le premier `save()`.
- **DashboardController** : pas de protection contre les requêtes vides (name/slug) — risque de requêtes lourdes.
- **Pas de policy/authorization** : les offres n'ont pas de `user_id`, donc pas de contrôle d'accès par utilisateur.

---

## 6. Plan de refactorisation incrémentale (6–10 étapes)

Chaque étape correspond à une PR de taille raisonnable.

| # | Étape | Fichiers | Description |
|---|-------|----------|-------------|
| 1 | Corriger la syntaxe du constrained eager loading API | `Api\OfferController` | Remplacer `with('products', fn...)` par `with(['products' => fn ($q) => $q->where('state', 'published')])`. |
| 2 | Extraire les FormRequest pour Offers | Nouveau `StoreOfferRequest`, `UpdateOfferRequest` | Centraliser la validation et rendre `image` nullable sur update. |
| 3 | Extraire les FormRequest pour Products | Nouveau `StoreProductRequest`, `UpdateProductRequest` | Centraliser la validation des produits. |
| 4 | Introduire des API Resources | `OfferResource`, `ProductResource` | Formater les réponses JSON de façon cohérente et masquer les champs sensibles. |
| 5 | Refactoriser OfferController (robustesse) | `OfferController` | Utiliser `findOrFail`, éviter les doubles `find`, simplifier la logique d'upload. |
| 6 | Sécuriser le DashboardController | `DashboardController` | Valider/sanitiser les paramètres de filtre, limiter les résultats (pagination). |
| 7 | Ajouter la pagination à l'API | `Api\OfferController` | Paginer `GET /api/offers` pour limiter le volume de données. |
| 8 | Améliorer la cohérence des réponses API | `Api\OfferController`, middleware | Gérer les erreurs (404, 422) et les réponses JSON de manière uniforme. |
| 9 | Ajouter des tests pour les endpoints critiques | `tests/Feature/` | Tests pour OfferController, ProductController, Api\OfferController. |
| 10 | Documenter l'API (optionnel) | `routes/api.php` ou doc externe | Lister les endpoints, paramètres et formats de réponse. |

---

## Résumé

- **Routes** : 16 web + 14 auth + 2 API.
- **Contrôleurs** : 5 principaux (Dashboard, Offer, Product, Api\Offer, Profile) + Auth.
- **Validation** : surtout inline ; 2 FormRequest (Profile, Login).
- **Uploads** : 4 actions (offers store/update, products store/update).
- **API** : 1 endpoint public (`/api/offers`), pas de Resource, syntaxe incorrecte du `with()`.
- **N+1** : pas de N+1 évident ; correction à faire sur la syntaxe du `with()` dans l'API.

Le plan ci-dessus peut être appliqué étape par étape, en commençant par les corrections et l'extraction des FormRequest.

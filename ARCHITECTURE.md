# Architecture guardrails

One-time reference for where code lives and how to keep the codebase consistent. No over-engineering; small diffs; domain logic testable without HTTP.

## Layer boundaries

| Layer | Path | Contents |
|-------|------|----------|
| **HTTP** | `app/Http/` | Controllers, FormRequests, Resources, Middleware. Controllers stay thin: authorize → validate (FormRequest) → call Action/Service → return Response/Resource/Redirect. |
| **Application (use-cases)** | `app/Actions/` | One-off operations (CreateOffer, UpdateProduct, ListDashboardOffers). Inject Repositories/Services; no direct Request in domain, pass validated data or DTOs where it helps. |
| **Domain** | `app/Enums/`, `app/Data/` | Enums (OfferState, ProductState), readonly DTOs/Data objects, domain rules. No framework imports in domain types. |
| **Infrastructure** | `app/Services/`, `app/Repositories/` | ImageStorage, OfferRepository, ProductRepository. Encapsulate I/O and queries. |
| **Policies** | `app/Policies/` | Authorization rules (OfferPolicy, ProductPolicy). Used by controllers via `authorize()`. |

## Preferences

- **Actions** for write/command use-cases; **QueryServices** or **Repositories** for read-only listing/filtering when it grows.
- **Policies** for authorization; **FormRequests** for validation; **Resources** for API responses (never return Eloquent models directly from API).
- **Enums** for state and fixed sets; OfferState/ProductState include `allowedTransitions()` and `canTransitionTo()` for domain rules; **Events/Listeners** and **Jobs** for async or side-effects (e.g. image processing).

## Rules

1. **Thin controllers**: no business logic in controllers; delegate to Actions/Services.
2. **Testable domain**: unit-test Actions, Repositories, and domain rules without HTTP (inject dependencies).
3. **Small diffs**: incremental changes; each step compiles and tests stay green.
4. **Explicit input**: avoid raw `$request->all()`; use validated data or whitelisted fields.
5. **Static analysis**: Larastan level 8; Pint for style. Fix root causes; suppress only with a short comment explaining why.

## Verification

- `make lint` — Pint (PSR-12)
- `make analyse` — PHPStan/Larastan (level 8)
- `make test` — PHPUnit
- `make quality` — lint + analyse + test

Run after each step so tests stay green before moving on.

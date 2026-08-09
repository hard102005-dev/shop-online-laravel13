# MASTER PROMPT: AI Development Kit & Execution Engine

> **System Orchestration File**: Reference this Master Prompt when prompting an AI coding assistant to implement features for this Laravel E-Commerce application.

---

## 1. System Identity & Core Directives
You are the Lead Laravel Architect and Senior Full-Stack Engineer for **ShopOnline E-Commerce**.
Your directive is to construct robust, high-performance, PSR-12 compliant code adhering strictly to the architecture and module specifications defined in `AI_DEVELOPMENT_KIT/`.

---

## 2. Mandatory Pre-Flight Verification Checklist
Before finishing any module or submitting code changes, verify:
- [x] Migration works cleanly (`php artisan migrate:fresh`).
- [x] Seeder works without errors (`php artisan db:seed`).
- [x] Route & Controller exist with RESTful resource naming.
- [x] Model relationship is correctly defined with explicit return types (`BelongsTo`, `HasMany`).
- [x] Validation is handled via Form Request classes (`App\Http\Requests\`).
- [x] Blade templates use Bootstrap 5 markup and are fully responsive.
- [x] No N+1 queries occur (all collections use eager loading `with()`).
- [x] Code strictly follows 4-Tier Layering: Controller -> Service -> Repository -> Model.
- [x] `php artisan test` passes with zero failures.

---

## 3. Module Reference Map

- **Core Rules**:
  - Persona & Rules: [00_SYSTEM_PROMPT.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/00_SYSTEM_PROMPT.md)
  - Project Overview: [01_PROJECT_OVERVIEW.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/01_PROJECT_OVERVIEW.md)
  - Service/Repo Architecture: [02_ARCHITECTURE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/02_ARCHITECTURE.md)
  - Database Rules: [03_DATABASE_RULES.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/03_DATABASE_RULES.md)
  - Coding Standards: [04_CODING_STANDARDS.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/04_CODING_STANDARDS.md)
  - Bootstrap 5 Guidelines: [05_UI_GUIDELINES.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/05_UI_GUIDELINES.md)
  - Security Rules: [06_SECURITY.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/06_SECURITY.md)
  - Testing Rules: [07_TESTING.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/07_TESTING.md)
  - Debug Workflow: [08_DEBUG_WORKFLOW.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/08_DEBUG_WORKFLOW.md)
  - Deployment Commands: [09_DEPLOYMENT.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/09_DEPLOYMENT.md)

- **Feature Modules**:
  - Auth Module: [10_AUTH_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/10_AUTH_MODULE.md)
  - Admin Module: [11_ADMIN_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/11_ADMIN_MODULE.md)
  - Category Module: [12_CATEGORY_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/12_CATEGORY_MODULE.md)
  - Product Module: [13_PRODUCT_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/13_PRODUCT_MODULE.md)
  - Cart Module: [14_CART_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/14_CART_MODULE.md)
  - Order Module: [15_ORDER_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/15_ORDER_MODULE.md)
  - Profile Module: [16_PROFILE_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/16_PROFILE_MODULE.md)
  - Report Module: [17_REPORT_MODULE.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/17_REPORT_MODULE.md)

- **Tracking & Logs**:
  - Task Board: [TODO.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/TODO.md)
  - Version Changelog: [CHANGELOG.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/CHANGELOG.md)

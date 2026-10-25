# Uni Swap (ACSC383 course project)

Uni Swap is a campus marketplace where university members swap, sell, or donate
things they no longer need, and see how much CO₂ and waste that reuse saves.
The client's main goal was environmental awareness rather than commerce, so the
impact numbers sit at the centre of the app.

This repository holds the course deliverables (requirements, diagrams, schema,
the client Q&A) and the working PHP application in `acsc383-swap-app/`.

## Features

- Sign up with a university email and verify the account before using the app.
  In this build the verification link is shown on screen instead of being
  emailed.
- List an item for **swap**, **sale** (fixed price) or **donation**, with title,
  description, category, condition, weight, and JPEG, PNG or WebP photos (up to
  5 MB each by default).
- Edit, delete, and track your own listings (available, reserved, completed).
- Browse available items with keyword search and filters for category,
  condition, and transaction type.
- Propose a transaction on someone else's item. The owner accepts or declines,
  either side can cancel while it is pending or accepted, and once accepted
  either side can mark it complete, which delists the item.
- Impact dashboard and history showing the CO₂ and waste saved through the
  completed transactions you took part in.

Impact is estimated from the item's weight and a per-category factor in
`src/Services/ImpactService.php`. Those factors are placeholder values for now,
not figures from the literature.

## Tech stack

- PHP 8 with no framework: a small router, controllers, services, and PHP views
- MySQL or MariaDB through PDO
- Plain CSS and JavaScript in `public/assets/`

## Run it locally (XAMPP)

1. Copy `acsc383-swap-app/` into your web root, for example
   `C:\xampp\htdocs\acsc383-swap-app`.
2. Create a database called `acsc383_swap_app`.
3. Import the five SQL files in `acsc383-swap-app/src/Database/Seed/` (users,
   items, item_images, transactions, email_verifications). Each file turns off
   foreign-key checks while it loads, so the order does not matter.
4. Check `acsc383-swap-app/.env`. The defaults expect MySQL on `127.0.0.1:3306`
   with user `root` and an empty password, and
   `APP_URL="http://localhost/acsc383-swap-app/public"`.
5. Open http://localhost/acsc383-swap-app/public/.

Pages are addressed with a `r` query parameter, for example
`index.php?r=browse` or `index.php?r=impact`.

The seeded items point to photos under `public/uploads/items/`, which are not in
the repository, so those listings show without images. Photos you upload are
saved there.

## Repository layout

```text
acsc383-swap-app/
  public/                 web root: index.php, CSS, JS, uploads/
  src/Core/               router, request/response, auth, validation, views, .env loader
  src/Controllers/        auth, items, transactions, dashboard, landing page
  src/Services/           database logic and the impact calculation
  src/Views/              PHP templates
  src/Database/Seed/      MySQL schema and sample data, one file per table
  .env                    local settings (no secrets, XAMPP defaults)
acsc383-swap-app.zip      zipped copy of the app as submitted
*.pdf                     user and system requirements, UML class and activity
                          diagrams, database schema, client Q&A, Figma link
```

## Course documents

| Document | What it covers |
| --- | --- |
| `ACSC383 - User Requirements Specification.pdf` | what users must be able to do |
| `ACSC383 - System Requirements Specification.pdf` | how the system must behave |
| `ACSC383 Project - Q and A.pdf` | the client's answers on scope, users, verification, impact |
| `ACSC383 - UML Class Diagram.pdf`, `ACSC383 - Activity Diagram.pdf` | design |
| `ACSC383 - Shcema.pdf` | database schema |
| `Figma Link.pdf` | link to the UI design in Figma |

## Known gaps

- Completing a transaction currently fails. `TransactionService::complete()`
  passes two values to an `UPDATE` with one placeholder, so PDO rejects it.
  The seeded completed transactions still show on the impact pages.
- Some items from the specification and the client Q&A are not implemented
  yet: password reset, profile editing, admin approval of listings, location
  filtering, and real email delivery.

## Support this project

If this project is useful to you, you can support its development with a one-off contribution via Revolut:

**[revolut.me/nadiotis](https://revolut.me/nadiotis)**

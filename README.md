# CMS — Full-Stack E-Commerce & Content Platform

A production-style Laravel application that combines a **customer-facing storefront**, a **Filament v3 admin panel**, and **real-time user-to-user messaging**. Built to demonstrate modern PHP architecture, Livewire interactivity, payment integration, and WebSocket broadcasting.

<p align="center">
  <!-- Optional: add your own badges after publishing -->
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=flat" alt="Livewire 3">
  <img src="https://img.shields.io/badge/Filament-3-FFB900?style=flat" alt="Filament 3">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat" alt="MIT License">
</p>

---

## Table of contents

- [Overview](#overview)
- [Key features](#key-features)
- [Tech stack](#tech-stack)
- [Architecture highlights](#architecture-highlights)
- [Screenshots](#screenshots)
- [Requirements](#requirements)
- [Installation](#installation)
- [Running the project locally](#running-the-project-locally)
- [Testing the chat system locally (step by step)](#testing-the-chat-system-locally-step-by-step)
- [Project structure](#project-structure)
- [Environment variables](#environment-variables)
- [Security note for public repos](#security-note-for-public-repos)
- [License](#license)

---

## Overview

**CMS** is not a simple CRUD demo. It is an integrated platform where:

1. **Customers** browse products and articles, manage a persistent cart (guest or logged-in), pay via **Stripe**, and chat with other users in **real time**.
2. **Administrators** use a dedicated **Filament** panel to manage catalog, content, orders, inventory, shipping, navigation, users, and **live support-style chat** with customers.

The same broadcasting layer (`MessageSent` + Laravel Reverb) powers chat on the public site and inside the admin panel (Livewire Echo listeners).

---

## Key features

### Storefront (public)

| Area | Details |
|------|---------|
| **Home** | Featured blog posts and latest products |
| **Catalog** | Product detail pages with variants, gallery (Spatie Media Library), categories |
| **Content** | Article/blog posts with slugs and categories |
| **Cart** | Session-based cart with UUID; merges when user logs in (`RestoreCartOnLogin`) |
| **Checkout** | Livewire checkout: customer info, saved addresses, shipping types, **Stripe** card payment |
| **Orders** | Order history and single-order view for authenticated users |
| **Real-time chat** | Private channels per user; conversation list, user search, read receipts, Laravel Echo + Reverb |

### Admin panel (`/admin`)

| Area | Details |
|------|---------|
| **Shop** | Products (media, categories, variations), orders (status workflow), stock, shipping types |
| **Content** | Posts (rich editor), categories, configurable navigation (header/sidebar JSON) |
| **Users** | User management (Jetstream-backed) |
| **Admin chat** | Full-page messenger UI; Livewire + Reverb for inbound messages without refresh |
| **UX** | Filament Themes (dark mode), language switch, grouped sidebar navigation |

### Authentication & API

- **Laravel Jetstream** — registration, profile photos, 2FA, session management  
- **Laravel Sanctum** — API token support  
- **Laravel Fortify** — auth actions and password rules  

---

## Tech stack

| Layer | Technologies |
|-------|----------------|
| **Backend** | PHP 8.2+, Laravel 11, Livewire 3 |
| **Admin** | Filament 3, Spatie Media Library, Tiptap editor, Themes & language plugins |
| **Frontend** | Blade, Tailwind CSS 4, Vite 7, Flowbite, Alpine.js (via Livewire) |
| **Real-time** | Laravel Reverb, Laravel Echo, Pusher protocol |
| **Payments** | Laravel Cashier + Stripe |
| **Database** | MySQL (migrations for full schema) |
| **Other** | `cknow/laravel-money`, adjacency-list package (categories), image optimizer |

---

## Architecture highlights

- **Service layer** — `CartManager` implements `Contract\CartManager` for cart create/sync/clear and guest-to-user merge.
- **Domain events** — `MessageSent` implements `ShouldBroadcastNow` on private channels `chat.{userId}`.
- **Channel authorization** — `routes/channels.php` restricts private chat channels to the authenticated user.
- **Livewire forms** — `CustomerCheckoutForm`, `AddressCheckoutForm` for structured checkout validation.
- **Enums** — `OrderStatus` for typed order lifecycle in admin tables.
- **Filament resources** — CRUD for all major entities with relation managers (e.g. product variations).
- **Dedicated admin chat page** — `AdminChatPage` (not a dashboard widget) with Filament Echo config in `config/filament.php`.

```
┌─────────────────┐     HTTP/API      ┌──────────────────┐
│  Storefront     │ ◄──────────────► │  Laravel 11      │
│  (Blade/LW)     │                   │  + Services      │
└────────┬────────┘                   └────────┬─────────┘
         │                                       │
         │ WebSocket (Echo)                      │ Filament /admin
         ▼                                       ▼
┌─────────────────┐                   ┌──────────────────┐
│  Laravel Reverb │ ◄── broadcast ─── │  MySQL           │
└─────────────────┘                   └──────────────────┘
```

---

## Screenshots

All images are stored in [`public/docs/screenshoots/`](public/docs/screenshoots/).  
When a page is tall, it is split into **multiple captures** (e.g. `home-1`, `home-2`) so the full UI is visible.

### Storefront

<table>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/home-1.png" alt="Home page part 1" width="100%"/><br/>
<strong>Home page (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/home-2.png" alt="Home page part 2" width="100%"/><br/>
<strong>Home page (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<img src="public/docs/screenshoots/home-3.png" alt="Home page part 3" width="100%"/><br/>
<strong>Home page (part 3)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/product-detail-1.png" alt="Product detail part 1" width="100%"/><br/>
<strong>Product detail (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/product-detail-2.png" alt="Product detail part 2" width="100%"/><br/>
<strong>Product detail (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-choose-product-variant.png" alt="Product variant selection part 1" width="100%"/><br/>
<strong>Product variant selection (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-choose-product-variant-2.png" alt="Product variant selection part 2" width="100%"/><br/>
<strong>Product variant selection (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/blog%20article-1.png" alt="Blog article part 1" width="100%"/><br/>
<strong>Blog article (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/blog%20article-2.png" alt="Blog article part 2" width="100%"/><br/>
<strong>Blog article (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/cart-1.png" alt="Shopping cart part 1" width="100%"/><br/>
<strong>Shopping cart (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/cart-2.png" alt="Shopping cart part 2" width="100%"/><br/>
<strong>Shopping cart (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="33%">
<img src="public/docs/screenshoots/checkout-1.png" alt="Checkout part 1" width="100%"/><br/>
<strong>Checkout (part 1)</strong>
</td>
<td align="center" width="33%">
<img src="public/docs/screenshoots/checkout-2.png" alt="Checkout part 2" width="100%"/><br/>
<strong>Checkout (part 2)</strong>
</td>
<td align="center" width="33%">
<img src="public/docs/screenshoots/checkout-3.png" alt="Checkout part 3" width="100%"/><br/>
<strong>Checkout (part 3)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/order-detail-1.png" alt="Order detail part 1" width="100%"/><br/>
<strong>Order detail (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/order-detail-2.png" alt="Order detail part 2" width="100%"/><br/>
<strong>Order detail (part 2)</strong>
</td>
</tr>
</table>

### Customer chat (real-time)

<table>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-conversation-list-1.png" alt="Conversations list part 1" width="100%"/><br/>
<strong>Conversations list (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-conversation-list-2.png" alt="Conversations list part 2" width="100%"/><br/>
<strong>Conversations list (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-active-chat-1.png" alt="Active chat part 1" width="100%"/><br/>
<strong>Active chat (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/user-active-chat-2.png" alt="Active chat part 2" width="100%"/><br/>
<strong>Active chat (part 2)</strong>
</td>
</tr>
</table>

### Admin panel (Filament)

<table>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-dashboard-1.png" alt="Admin dashboard part 1" width="100%"/><br/>
<strong>Admin dashboard (part 1 — top of page)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-dashboard-2.png" alt="Admin dashboard part 2" width="100%"/><br/>
<strong>Admin dashboard (part 2 — rest of page)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/category-1.png" alt="Category page part 1" width="100%"/><br/>
<strong>Admin Category page (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/category-2.png" alt="Category page part 2" width="100%"/><br/>
<strong>Admin Category page (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<img src="public/docs/screenshoots/admin-product-list-1.png" alt="Admin products list" width="100%"/><br/>
<strong>Admin — products list</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-product-edit-1.png" alt="Admin product edit part 1" width="100%"/><br/>
<strong>Admin — product edit (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-product-edit-2.png" alt="Admin product edit part 2" width="100%"/><br/>
<strong>Admin — product edit (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-product-edit-3.png" alt="Admin product edit part 3" width="100%"/><br/>
<strong>Admin — product edit (part 3)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-product-edit-4.png" alt="Admin product edit part 4" width="100%"/><br/>
<strong>Admin — product edit (part 4)</strong>
</td>
</tr>
<tr>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-orders-1.png" alt="Admin orders part 1" width="100%"/><br/>
<strong>Admin — orders (part 1)</strong>
</td>
<td align="center" width="50%">
<img src="public/docs/screenshoots/admin-orders-2.png" alt="Admin orders part 2" width="100%"/><br/>
<strong>Admin — orders (part 2)</strong>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<img src="public/docs/screenshoots/admin-posts.png" alt="Admin posts" width="100%"/><br/>
<strong>Admin — posts management</strong>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<img src="public/docs/screenshoots/admin-conversation-list.png" alt="Admin chat conversations" width="100%"/><br/>
<strong>Admin — chat conversations sidebar</strong>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<img src="public/docs/screenshoots/admin-active-chat-1.png" alt="Admin active chat" width="100%"/><br/>
<strong>Admin — active chat (real-time)</strong>
</td>
</tr>
</table>

---

## Requirements

- PHP **8.2+** with extensions: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer 2.x  
- Node.js **18+** and npm  
- MySQL 8.x (or MariaDB)  
- [Stripe](https://stripe.com) account (test keys) for checkout  
- For real-time features: ability to run **Reverb** and **Vite** dev server (or use `npm run build` in production)

---

## Installation

```bash
# Clone the repository
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git
cd YOUR_REPO

# PHP dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Configure .env (database, Stripe, Reverb — see below)
# Create database, then:
php artisan migrate

# Frontend
npm install
npm run build

# Storage link (media uploads)
php artisan storage:link
```

Create an admin user via Tinker or register through the app and promote the user in the database as needed for `/admin` access (Filament uses the same `users` table).

```bash
php artisan make:filament-user
```

---

## Running the project locally

Use **three terminals** for the full stack (HTTP app, WebSockets, frontend assets):

```bash
# Terminal 1 — Laravel (HTTP)
php artisan serve --host=0.0.0.0 --port=8080

# Terminal 2 — Reverb (WebSockets) — use a different port than Laravel
php artisan reverb:start

# Terminal 3 — Vite (development assets)
npm run dev
```

> **Ports:** Laravel on **8080**, Reverb on **8081** (or any free port ≠ 8080), Vite on **5173**.  
> If you only use `npm run build`, you can skip Terminal 3.

**Stripe (checkout):**

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

---

## Testing the chat system locally (step by step)

Follow these steps to test **real-time chat** on one machine (two browser tabs) or between a **laptop and a phone** on the same Wi‑Fi.

### Step 1 — Find your computer’s LAN IP address

On **Windows**, open **Command Prompt** or **PowerShell** and run:

```bat
ipconfig
```

Look for your active adapter (usually **Wireless LAN adapter Wi‑Fi** or **Ethernet**). Note the **IPv4 Address**, for example:

```text
IPv4 Address. . . . . . . . . . . : 192.168.1.5
```

Use this IP below as `YOUR_LAN_IP` (example: `192.168.1.5`).

### Step 2 — Choose ports (do not reuse the same port)

| Service | Variable / command | Example port |
|---------|-------------------|--------------|
| Laravel HTTP | `php artisan serve --port=8080` | **8080** |
| Laravel Reverb | `REVERB_SERVER_PORT` / `REVERB_PORT` | **8081** |
| Vite dev server | `npm run dev` | **5173** |

Reverb **must not** use the same port as `php artisan serve`.

### Step 3 — Update `.env`

Replace `YOUR_LAN_IP` with the address from Step 1.

```env
APP_URL=http://YOUR_LAN_IP:8080

BROADCAST_DRIVER=reverb

# Reverb process (bind on all interfaces)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8081

# What browsers and Laravel use to connect to Reverb
REVERB_HOST=YOUR_LAN_IP
REVERB_PORT=8081
REVERB_SCHEME=http

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret

# Frontend (Echo)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=YOUR_LAN_IP
VITE_REVERB_PORT=8081
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Required when using npm run dev from another device on the network
VITE_DEV_SERVER_URL=http://YOUR_LAN_IP:5173
```

After saving:

```bash
php artisan config:clear
```

### Step 4 — Configure Vite for LAN access (if using `npm run dev`)

In `vite.config.js`, ensure the dev server is reachable from other devices:

```js
server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    hmr: {
        host: 'YOUR_LAN_IP',
    },
},
```

If you see **CORS errors** loading scripts from port `5173`, keep `cors: true` or allow your `APP_URL` origin explicitly.

**Alternative:** run `npm run build` once and skip Vite during testing (no cross-origin dev server).

### Step 5 — Allow ports in Windows Firewall

Allow inbound connections for:

- **8080** (Laravel)
- **8081** (Reverb)
- **5173** (Vite — only if using `npm run dev` on a phone)

### Step 6 — Start all services

```bash
# Terminal 1
php artisan serve --host=0.0.0.0 --port=8080

# Terminal 2
php artisan reverb:start

# Terminal 3 (optional if not using npm run build)
npm run dev
```

### Step 7 — Open the app with the same host everywhere

| Device | URL |
|--------|-----|
| Laptop | `http://YOUR_LAN_IP:8080` |
| Phone (same Wi‑Fi) | `http://YOUR_LAN_IP:8080` |

Avoid mixing `http://localhost:8080` on the laptop and `http://YOUR_LAN_IP:8080` on the phone — use the **LAN IP on both** for consistent sessions and broadcasting.

### Step 8 — Test customer chat

1. Register or log in as **User A** on the laptop.
2. Log in as **User B** on the phone (or another browser profile).
3. Open **Conversations** → start a chat: `/conversations` then `/chat/{user}`.
4. Send a message from User B → it should appear on User A **without refresh**.
5. Send from User A → it should appear on User B.

**Browser console checks:**

- `✅ Echo initialized successfully`
- `✅ Reverb connected`
- No CORS errors for `:5173` or WebSocket failures on `:8081`

### Step 9 — Test admin chat

1. Create a Filament admin user: `php artisan make:filament-user`
2. Open `http://YOUR_LAN_IP:8080/admin/chat`
3. Select a user who has messaged you.
4. Send/receive messages in real time (Filament loads Echo via `config/filament.php`).

### Troubleshooting

| Problem | What to check |
|---------|----------------|
| `Echo not detected` on phone | Vite CORS / `VITE_DEV_SERVER_URL`; or use `npm run build` |
| Messages only work one way | Both devices must use `YOUR_LAN_IP`, not `localhost` |
| WebSocket connection failed | Reverb running, `REVERB_PORT=8081`, firewall open |
| 419 on `/broadcasting/auth` | Logged in, CSRF meta tag present, same origin as `APP_URL` |

---

## Project structure

```
app/
├── Enums/              # OrderStatus, etc.
├── Events/             # MessageSent (broadcasting)
├── Filament/
│   ├── Pages/          # AdminChatPage
│   └── Resources/      # Products, Orders, Posts, …
├── Http/Controllers/   # Storefront + chat API
├── Listeners/          # RestoreCartOnLogin
├── Livewire/           # Cart, Checkout, Product, Navigation, …
├── Models/             # Eloquent models
└── Services/           # CartManager (+ contract)

resources/views/
├── chats/              # Customer chat UI
├── filament/pages/     # Admin chat UI
└── livewire/           # Interactive storefront components

routes/
├── web.php             # Storefront + chat routes
└── channels.php        # Private chat channel auth
```

---

## Environment variables

| Variable | Purpose |
|----------|---------|
| `DB_*` | MySQL connection |
| `BROADCAST_DRIVER` | Set to `reverb` for chat |
| `REVERB_*` / `VITE_REVERB_*` | WebSocket server + frontend Echo |
| `STRIPE_KEY` / `STRIPE_SECRET` | Payments |
| `APP_URL` | Must match how you access the app (important for broadcasting & Vite) |

See `.env.example` for defaults. Copy and extend with Reverb/Stripe values before running.

---

## Security note for public repos

- **Never commit** `.env`, Stripe secrets, or Reverb keys.  
- Use Stripe **test mode** in screenshots.  
- Redact personal emails and addresses in README images.  
- Review `git status` before pushing.

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  <sub>Built with Laravel · Livewire · Filament · Reverb · Stripe</sub>
</p>

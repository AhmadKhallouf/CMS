# Screenshot checklist for README

Place PNG/WebP files in this folder and link them from the root `README.md`.

## Naming convention

```
01-home.png
02-product.png
03-category.png
04-article.png
05-cart.png
06-checkout.png
07-order-detail.png
08-conversations.png
09-chat-active.png
10-admin-dashboard.png
11-admin-products.png
12-admin-product-edit.png
13-admin-orders.png
14-admin-chat.png
15-admin-posts.png
```

## Minimum for GitHub (6–8 images)

1. `01-home.png` — `/`
2. `02-product.png` — `/products/{slug}`
3. `05-cart.png` — `/cart`
4. `06-checkout.png` — `/checkout` (use Stripe test card, blur PAN if shown)
5. `09-chat-active.png` — `/chat/{user}` with visible messages
6. `10-admin-dashboard.png` — `/admin`
7. `13-admin-orders.png` — `/admin/orders`
8. `14-admin-chat.png` — `/admin/chat` (prefer dark mode)

## Tips

- Resolution: 1440×900 or 1920×1080
- Use sample/fake user names and emails
- Dark mode: Filament Themes toggle + admin chat
- Optional GIF: `09-chat-realtime.gif` showing a message arriving without refresh

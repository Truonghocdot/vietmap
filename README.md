# Vietmap Filament Clone

Laravel 13 + Filament 5 clone giao dien `thuevietmap.vn`, co luong:

- Khach chon goi tren homepage clone.
- Xac nhan don hang va quet QR SePay.
- Webhook SePay doi soat thanh toan.
- He thong tu reserve key, nhan thanh cong thi tu giao tai khoan/key.
- Neu khach nhap Gmail, thong tin duoc gui qua mail tu dong.

## Thanh phan da co

- Homepage clone tu mirror site hien tai.
- Trang xac nhan don hang, trang thanh toan, trang tra cuu don, lich su 30 ngay theo IP.
- Admin Filament quan ly `goi`, `key`, `coupon`, `don hang`, `webhook logs`.
- Seed demo de chay thu ngay.

## Cai dat nhanh

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Cau hinh SePay

Can dien cac bien trong `.env`:

```env
SEPAY_WEBHOOK_API_KEY=
SEPAY_ACCOUNT_NUMBER=
SEPAY_ACCOUNT_NAME=
SEPAY_BANK_CODE=
SEPAY_QR_TEMPLATE=compact2
SEPAY_ORDER_EXPIRATION_MINUTES=30
```

Webhook endpoint:

```text
POST /api/webhooks/sepay
```

Header xac thuc theo tai lieu SePay:

```text
Authorization: Apikey YOUR_API_KEY
```

## Admin Filament

Panel admin nam tai:

```text
/admin
```

Tao user admin bang lenh:

```bash
php artisan make:filament-user
```

## Seed demo

Seeder mac dinh tao:

- 9 goi Vietmap theo site hien tai
- 3 key mau moi goi
- 2 coupon mau: `VIETMAP10`, `GIAM15000`

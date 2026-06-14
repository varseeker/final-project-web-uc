# Deploy POS Warkop Kayu ke Render.com

Panduan deploy **POS (Kasir)** dan menghubungkannya dengan **Inventory Management** (`warkop-inventory`) di Render.

## Arsitektur di Render

```
┌─────────────────────────┐         Bearer token          ┌──────────────────────────┐
│  pos-warkop-kayu (POS)  │ ─── INVENTORY_API_TOKEN ───► │  warkop-inventory (API)  │
│  uc_master-main         │     GET /api/menus           │  inventory-system        │
│                         │     POST /api/orders         │                          │
└───────────┬─────────────┘                              └────────────┬─────────────┘
            │                                                         │
            │              PostgreSQL warkop-db (shared)               │
            └──────────────────────────┬──────────────────────────────┘
                                       │
              POS: tabel pos_* + pos_sessions
              Inventory: users, menus, pos_orders, dll.
```

**Urutan deploy yang benar:** Inventory dulu → POS kedua.

---

## Langkah 1 — Deploy Inventory (jika belum)

Ikuti `DEPLOY.md` di repo **inventory-system** atau:

1. Push repo `inventory-system` ke GitHub
2. Render → **New** → **Blueprint** → pilih repo
3. Tunggu `warkop-inventory` + `warkop-db` selesai

---

## Langkah 2 — Deploy POS

1. Push repo `uc_master-main` ke GitHub (repo terpisah)
2. Render → **New** → **Blueprint** → pilih repo POS
3. Render membuat web service `pos-warkop-kayu`

> POS **tidak** otomatis terhubung ke database Inventory karena blueprint terpisah. Langkah 3 wajib dilakukan manual.

---

## Langkah 3 — Environment variables (wajib)

Buka **Render Dashboard** dan isi variabel berikut. Gunakan tab **Environment** pada masing-masing service.

### A. Service `warkop-inventory` (Inventory)

| Variabel | Nilai contoh | Wajib | Keterangan |
|----------|--------------|:-----:|------------|
| `APP_KEY` | `base64:...` | ✅ | `php artisan key:generate --show` — **unik untuk inventory** |
| `APP_URL` | `https://warkop-inventory.onrender.com` | ✅ | URL service inventory, **https**, tanpa `/` di akhir |
| `INVENTORY_API_TOKEN` | `warkop-pos-secret-2026-xyz` | ✅ | Token rahasia — **harus sama** dengan POS |
| `POS_SERVICE_URL` | `https://pos-warkop-kayu.onrender.com` | ✅ | Link menu "Kasir (POS)" di sidebar inventory |
| `DATABASE_URL` | *(otomatis dari warkop-db)* | ✅ | Dari blueprint; jika error DNS, pakai **External Database URL** |
| `DB_URL` | *(sama dengan DATABASE_URL)* | ✅ | Duplikat URL database |
| `DB_CONNECTION` | `pgsql` | ✅ | Sudah di render.yaml |
| `DB_SSLMODE` | `require` | ✅ | Sudah di render.yaml |
| `RENDER_REGION` | `singapore` | ✅ | Sudah di render.yaml |

**Jangan ubah** `INVENTORY_POS_USER_EMAIL` kecuali Anda mengubah seeder — default `pos-integration@system.local`.

---

### B. Service `pos-warkop-kayu` (POS)

| Variabel | Nilai contoh | Wajib | Keterangan |
|----------|--------------|:-----:|------------|
| `APP_KEY` | `base64:...` | ✅ | Generate **baru** (beda dari inventory!) |
| `APP_URL` | `https://pos-warkop-kayu.onrender.com` | ✅ | URL service POS, **https**, tanpa `/` di akhir |
| `DATABASE_URL` | *(External URL warkop-db)* | ✅ | Salin dari PostgreSQL `warkop-db` → tab **Info** → **External Database URL** |
| `DB_URL` | *(sama dengan DATABASE_URL)* | ✅ | Duplikat URL database |
| `DB_CONNECTION` | `pgsql` | ✅ | Sudah di render.yaml |
| `DB_PREFIX` | `pos_` | ✅ | Tabel POS pakai prefix; tabel `users` inventory **tanpa** prefix |
| `DB_SSLMODE` | `require` | ✅ | Sudah di render.yaml |
| `RENDER_REGION` | `singapore` | ✅ | Sudah di render.yaml |
| `INVENTORY_SERVICE_ENABLED` | `true` | ✅ | Sudah di render.yaml |
| `INVENTORY_SERVICE_URL` | `https://warkop-inventory.onrender.com` | ✅ | Base URL inventory (tanpa `/api`) |
| `INVENTORY_API_TOKEN` | `warkop-pos-secret-2026-xyz` | ✅ | **Harus identik** dengan inventory |
| `MIDTRANS_SERVER_KEY` | `SB-Mid-server-...` | ✅ | Sandbox/production Midtrans |
| `MIDTRANS_CLIENT_KEY` | `SB-Mid-client-...` | ✅ | Sandbox/production Midtrans |

**Opsional (sudah ada default di render.yaml):**

| Variabel | Default |
|----------|---------|
| `INVENTORY_SYNC_TTL` | `120` |
| `INVENTORY_DEFAULT_PRICE` | `18000` |
| `INVENTORY_ORDER_SOURCE` | `pos-warkop-kayu` |
| `INVENTORY_ORDER_ID_PREFIX` | `pos-warkop-kayu` |
| `INVENTORY_TIMEOUT_SECONDS` | `30` |

---

## Langkah 4 — Token API (penghubung utama)

1. Buat string acak panjang, contoh:
   ```
   warkop-pos-secret-2026-xyz
   ```
2. Set **nilai yang sama persis** di:
   - `warkop-inventory` → `INVENTORY_API_TOKEN`
   - `pos-warkop-kayu` → `INVENTORY_API_TOKEN`
3. Redeploy **kedua** service setelah menyimpan.

POS memanggil inventory dengan header:
```
Authorization: Bearer <INVENTORY_API_TOKEN>
```

---

## Langkah 5 — Database shared

POS dan Inventory memakai **database PostgreSQL yang sama** (`warkop-db`):

| Aplikasi | Tabel | Prefix |
|----------|-------|--------|
| Inventory | `users`, `menus`, `pos_orders`, ... | *(tanpa prefix)* |
| POS | `pos_menus`, `pos_baskets`, `pos_orders`, ... | `pos_` |

**Cara set di POS:**

1. Render → **PostgreSQL** `warkop-db` → **Info**
2. Salin **External Database URL**:
   ```
   postgresql://warkop:xxxx@dpg-xxxx-a.singapore-postgres.render.com/warkop_db
   ```
3. POS service → **Environment** → set `DATABASE_URL` dan `DB_URL` = URL tersebut
4. **Hapus** variabel terpisah jika ada: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
5. Redeploy POS

> Script `docker/fix-render-env.sh` otomatis memperbaiki hostname internal Render saat container start.

---

## Langkah 6 — Verifikasi koneksi

### Inventory sudah jalan

1. Buka `https://warkop-inventory.onrender.com/login`
2. Login: `letoy@warkopkayu.test` / `password` (setelah seeder)

### POS terhubung ke inventory

1. **Bangunkan inventory dulu** (tier gratis tidur ~15 menit) — buka URL inventory, tunggu halaman login muncul
2. Buka `https://pos-warkop-kayu.onrender.com/login`
3. Login staf: `letoy@warkopkayu.test` / `password`
4. Halaman `/home` harus menampilkan **katalog menu** dari inventory

### Cek log POS jika menu kosong

Dashboard → `pos-warkop-kayu` → **Logs**, cari:

```
WARNING: Menu sync gagal — cek INVENTORY_SERVICE_URL dan INVENTORY_API_TOKEN.
```

Penyebab umum:

| Gejala | Penyebab | Perbaikan |
|--------|----------|-----------|
| Menu kosong | Inventory masih cold start | Buka inventory dulu, refresh POS |
| 401 Invalid token | Token tidak sama | Samakan `INVENTORY_API_TOKEN` di kedua service |
| Connection refused | URL salah | `INVENTORY_SERVICE_URL` = URL inventory tanpa path |
| Database error | URL DB salah | Pakai External Database URL di POS |

### Cek API manual (opsional)

```bash
curl -H "Authorization: Bearer warkop-pos-secret-2026-xyz" \
  https://warkop-inventory.onrender.com/api/menus
```

Harus mengembalikan JSON dengan `"data": [...]`.

---

## Login POS

Hanya akun dengan **role `staff`** di tabel `users` inventory yang bisa login POS:

| Email | Password | Role |
|-------|----------|------|
| `letoy@warkopkayu.test` | `password` | staff |
| `ketoy@warkopkayu.test` | `password` | staff |

Admin/owner **tidak** bisa login POS (by design).

---

## Checklist cepat

```
Inventory (warkop-inventory):
  [ ] APP_KEY (base64:...)
  [ ] APP_URL = https://warkop-inventory.onrender.com
  [ ] INVENTORY_API_TOKEN = <token rahasia>
  [ ] POS_SERVICE_URL = https://pos-warkop-kayu.onrender.com
  [ ] DATABASE_URL / DB_URL dari warkop-db

POS (pos-warkop-kayu):
  [ ] APP_KEY (base64:...) — berbeda dari inventory
  [ ] APP_URL = https://pos-warkop-kayu.onrender.com
  [ ] DATABASE_URL / DB_URL = External URL warkop-db (sama DB)
  [ ] INVENTORY_SERVICE_URL = https://warkop-inventory.onrender.com
  [ ] INVENTORY_API_TOKEN = <token yang sama>
  [ ] MIDTRANS_SERVER_KEY + MIDTRANS_CLIENT_KEY
  [ ] INVENTORY_SERVICE_ENABLED = true
```

---

## Troubleshooting

### APP_KEY salah

Format wajib: `base64:xxxxxxxx...`

Generate: `php artisan key:generate --show`

### CSS POS tidak load

Set `APP_URL` POS ke URL Render dengan `https://`.

### Pesanan POS tidak muncul di laporan inventory

- Pastikan `INVENTORY_API_TOKEN` sama
- Cek log POS saat checkout — error push order ke `/api/orders`
- Pastikan menu di inventory berstatus aktif untuk POS

### Database `could not translate host name "dpg-..."`

Set `DATABASE_URL` ke **External Database URL** (bukan internal hostname pendek).

---

## Update aplikasi

Push ke GitHub → Render auto-deploy. Migrasi database jalan otomatis saat container start (`docker/entrypoint.sh`).

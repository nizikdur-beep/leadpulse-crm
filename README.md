# LeadPulse Mini Framework — PHP MVC CRM

LeadPulse — bu PHP 8.2+ va MySQL 8+ / MariaDB asosida yaratilgan, ortiqcha tashqi kutubxonalarsiz (Zero-Dependency), toza va kengaytiriladigan **Custom PHP MVC CRM Mini Framework**.

Tizim kichik va oʻrta bizneslar, savdo boʻlimlari va xizmat koʻrsatish korxonalari uchun sotuv voronkasi (pipeline), mijozlar bazasi, topshiriqlar, kalendar va hisobotlarni qulay boshqarish imkonini beradi.

---

## 🚀 Asosiy Xususiyatlari

1. **Mustaqil Mini Framework**:
   - Custom tejamkor Router (GET, POST, PUT, PATCH, DELETE va HTML `_method` override).
   - Singleton PDO maʼlumotlar bazasi boshqaruvi va tayyorlangan soʻrovlar (Prepared Statements).
   - Yengil Active Record Model (CRUD, Soft Delete, Pagination, Mass-assignment himoyasi).
   - Xavfsiz Shablizator (Views, Layouts, XSS himoyasi, CSRF token helperi).
   - Middleware mexanizmi (`auth`, `guest`, `admin`, `csrf`).
   - Centralized Exception Handling va xavfsiz loglash (`storage/logs/app.log`).

2. **Xavfsizlik (Security-First)**:
   - **SQL Injection**: 100% PDO parametrli soʻrovlar.
   - **XSS Himoyasi**: Barcha foydalanuvchi maʼlumotlari `htmlspecialchars` bilan filtrlanadi.
   - **CSRF Token**: Barcha POST/PUT/DELETE formalar uchun token tekshiruvi (419 Page Expired).
   - **Parollar**: PHP `password_hash()` (Bcrypt/Argon2ID) va login paytida avtomatik rehash.
   - **Session Security**: `HttpOnly`, `SameSite=Lax`, login paytida `session_regenerate_id()`.
   - **Brute-Force & Rate Limiting**: 5 ta muvaffaqiyatsiz login urinishidan soʻng 15 daqiqaga bloklash.
   - **IDOR Himoyasi**: Managerlar faqat oʻziga tegishli mijoz va bitimlarni koʻra oladi/tahrirlay oladi; begona ID ochilsa 403 Forbidden beriladi.
   - **CSV Formula Injection**: Eksport qilinadigan kataklar `=, +, -, @` bilan boshlansa apostrof bilan zararsizlantiriladi.

3. **Toʻliq CRM Modullari**:
   - **Dashboard**: Real vaqtdagi KPI kartalar (Jami mijozlar, Faol bitimlar, Umumiy tushum, Konversiya foizi), oylik savdo dinamikasi bar charti, yaqinlashayotgan vazifalar va soʻnggi harakatlar.
   - **Mijozlar (Clients)**: Toʻliq CRUD, qidiruv, sahifalash, saralash, batafsil profil, bogʻlangan bitimlar tarixi va takroriy mijozlarni (telefon/email) aniqlash.
   - **Bitimlar (Leads & Pipeline)**: Interaktiv Kanban/Voronka koʻrinishi (Yangi, Aloqa, Taklif, Yakun/Won, Yoʻqotilgan/Lost), tezkor bosqich oʻzgartirish, yoʻqotish sabablari va bosqichlar tarixi (Audit log).
   - **Vazifalar (Tasks)**: Muddatlar (due date), muhimlik darajalari (past, oʻrta, yuqori), kechikkan vazifalar ogohlantirishi va bir bosishda bajarilgan deb belgilash.
   - **Kalendar (Calendar)**: Oylik interaktiv kalendar, vazifalar va eslatmalar bilan integratsiya.
   - **Faoliyatlar (Activities)**: Qoʻngʻiroq, uchrashuv, email, xabar va eslatmalar xronologiyasi.
   - **Hisobotlar (Reports)**: Bosqichlar taqsimoti, menejerlar samaradorligi jadvali va Excel moslashuvchan (UTF-8 BOM) CSV eksport.
   - **Xodimlar (Users — Admin Only)**: Menejerlar qoʻshish, faolligini boshqarish va rollar tayinlash.

---

## 🛠 Tizim Talablari

- **PHP**: 8.2 yoki undan yuqori (tavsiya etiladi: PHP 8.3+)
- **PHP Kengaytmalari**: `pdo`, `pdo_mysql`, `mbstring`, `session`, `json`, `openssl`
- **Maʼlumotlar bazasi**: MySQL 8.0+ yoki MariaDB 10.3+
- **Veb-server**: Apache (mod_rewrite bilan), Nginx yoki PHP Built-in Server.
- **Tashqi kutubxonalar**: Talab etilmaydi! Composer oʻrnatilmagan boʻlsa ham loyiha toʻliq ishlaydi.

---

## 📂 Loyiha Tuzilmasi

```
leadpulse/
├── app/
│   ├── Core/                  # Framework yadrosi (Router, Database, Model, View, Auth, Validator, Csrf, Session)
│   ├── Controllers/           # HTTP controllerlar (Dashboard, Client, Lead, Task, Report, User...)
│   ├── Models/                # DB bilan ishlovchi modellar (User, Client, Lead, Task, Activity, History)
│   ├── Services/              # Biznes logikasi (LeadService, ReportService, DuplicateDetector)
│   ├── Middleware/            # Himoya qatlami (Auth, Guest, Admin, Csrf)
│   └── Views/                 # UI shablonlari (layouts, dashboard, clients, leads, tasks, reports, errors)
├── config/
│   ├── app.php                # Asosiy sozlamalar
│   └── database.php           # DB ulanish sozlamalari
├── database/
│   ├── migrations/
│   │   ├── 001_create_tables.sql  # Toʻliq SQL sxemasi
│   │   └── migrate.php            # Avtomatik migratsiya oʻtkazuvchi CLI skript
│   └── seeders/
│       └── seed.php               # Demo maʼlumotlar va birinchi adminni yaratuvchi skript
├── public/
│   ├── assets/
│   │   ├── css/app.css        # Custom B2B SaaS dizayni (100% responsive)
│   │   └── js/app.js          # Interaktiv boshqaruv skriptlari
│   ├── .htaccess              # Apache yoʻnaltirish qoidalari
│   └── index.php              # Barcha soʻrovlar kirish nuqtasi (Front Controller)
├── routes/
│   └── web.php                # Barcha URL marshrutlari
├── storage/
│   └── logs/app.log           # Xatoliklar jurnali
├── tests/
│   └── run_tests.php          # Avtomatlashtirilgan test toʻplami
├── .env.example
├── composer.json
└── README.md
```

---

## ⚙️ Oʻrnatish va Ishga Tushirish

### 1. Konfiguratsiyani sozlash
Loyiha papkasidagi `.env.example` faylidan `.env` nusxasini yarating:
```bash
cp .env.example .env
```
`.env` faylini ochib, MySQL maʼlumotlaringizni kiriting:
```ini
APP_NAME="LeadPulse CRM"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leadpulse
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Maʼlumotlar bazasi va migratsiyalarni ishga tushirish
MySQL serveringiz yoniq ekanligiga ishonch hosil qiling va CLI orqali quyidagi buyruqni bering:
```bash
php database/migrations/migrate.php
```
*Bu buyruq `leadpulse` maʼlumotlar bazasini va barcha kerakli jadvallarni, indekslarni hamda foreign key bogʻlanishlarini avtomatik yaratadi.*

### 3. Demo maʼlumotlar va Bosh Adminni kiritish (Seeder)
Tizimda bosh admin va namunaviy menejerlar, mijozlar va bitimlarni yaratish uchun:
```bash
php database/seeders/seed.php
```

Yaratiladigan standart hisoblar:
- **Bosh Administrator**:
  - Email: `admin@leadpulse.uz`
  - Parol: `Admin@12345`
- **Sotuv Menejeri (Aziz Rahimov)**:
  - Email: `aziz@leadpulse.uz`
  - Parol: `Manager@123`
- **Sotuv Menejeri (Nodira Salimova)**:
  - Email: `nodira@leadpulse.uz`
  - Parol: `Manager@123`

### 4. Lokal serverni ishga tushirish
Hech qanday qoʻshimcha dasturlarsiz, PHP ning oʻrnatilgan veb-serveridan foydalanishingiz mumkin:
```bash
php -S localhost:8000 -t public
```
Brauzeringizda oching: **[http://localhost:8000](http://localhost:8000)**

---

## 🌐 Shared Hostingga Joylashtirish

1. Loyiha fayllarini hosting serveringizga yuklang (masalan, `public_html` yoki uning yoniga).
2. **Document Root** ni `public/` katalogiga yoʻnaltiring (masalan cPanel -> Domains boʻlimida).
3. Agar asosiy domen bevosita `public_html` ga ulangan boʻlsa, loyiha ildizidagi `.htaccess` barcha soʻrovlarni xavfsiz ravishda `public/` ga yoʻnaltiradi.
4. `storage/logs/` katalogiga yozish huquqini (`chmod 775` yoki `777`) bering.
5. Production muhitida `.env` ichida `APP_DEBUG=false` va `APP_ENV=production` qilib belgilang.

---

## 🧪 Avtomatlashtirilgan Testlarni Ishga Tushirish

Barcha funksiyalar va xavfsizlik choralarini avtomatik tekshirish uchun:
```bash
php tests/run_tests.php
```
Ushbu test quyidagilarni toʻliq tekshiradi:
- Barcha PHP fayllari sintaksisi
- CSRF token generatsiyasi va validatsiyasi
- XSS xavfsizligi
- CSV formula injection filtrlashi
- Server-side validator
- Router va `_method` override
- Konversiya formulalari hisob-kitobi
- 404 xato sahifalari

---

## 📌 Asosiy URL Marshrutlari (Routes)

| Metod | URI | Tavsif | Ruxsat |
|---|---|---|---|
| GET | `/login` | Tizimga kirish sahifasi | Mehmon (Guest) |
| POST | `/login` | Autentifikatsiya qilish | Mehmon |
| POST | `/logout` | Tizimdan chiqish | Login qilganlar |
| GET | `/dashboard` | CRM bosh sahifasi (KPI va grafiklar) | Barcha xodimlar |
| GET | `/clients` | Mijozlar roʻyxati va filtrlar | Barcha xodimlar |
| GET | `/clients/create` | Yangi mijoz qoʻshish formasi | Barcha xodimlar |
| POST | `/clients` | Yangi mijozni saqlash | Barcha xodimlar |
| GET | `/clients/{id}` | Mijoz profili, bitimlar va faoliyatlar | Egasi / Admin |
| GET | `/clients/{id}/edit` | Mijozni tahrirlash | Egasi / Admin |
| POST | `/clients/{id}/update` | Mijozni yangilash | Egasi / Admin |
| POST | `/clients/{id}/delete` | Mijozni oʻchirish (Soft delete) | Egasi / Admin |
| GET | `/leads` | Bitimlar (Voronka / Jadval) | Barcha xodimlar |
| GET | `/leads/create` | Yangi bitim qoʻshish formasi | Barcha xodimlar |
| POST | `/leads` | Yangi bitimni saqlash | Barcha xodimlar |
| GET | `/leads/{id}` | Bitim kartasi va bosqichlar tarixi | Egasi / Admin |
| GET | `/leads/{id}/edit` | Bitimni tahrirlash | Egasi / Admin |
| POST | `/leads/{id}/update` | Bitimni yangilash | Egasi / Admin |
| POST | `/leads/{id}/stage` | Bitim bosqichini oʻzgartirish | Egasi / Admin |
| POST | `/leads/{id}/delete` | Bitimni oʻchirish | Egasi / Admin |
| GET | `/tasks` | Vazifalar roʻyxati va filtrlar | Barcha xodimlar |
| GET | `/tasks/create` | Yangi vazifa yaratish formasi | Barcha xodimlar |
| POST | `/tasks` | Yangi vazifani saqlash | Barcha xodimlar |
| GET | `/tasks/{id}/edit` | Vazifani tahrirlash | Egasi / Admin |
| POST | `/tasks/{id}/update` | Vazifani yangilash | Egasi / Admin |
| POST | `/tasks/{id}/complete`| Vazifani «Bajarildi» deb belgilash | Egasi / Admin |
| GET | `/calendar` | Oylik interaktiv reja kalendari | Barcha xodimlar |
| GET | `/activities` | Barcha murojaat va faoliyatlar tarixi | Barcha xodimlar |
| POST | `/activities` | Yangi faoliyat qayd etish | Barcha xodimlar |
| GET | `/reports` | Savdo va samaradorlik hisobotlari | Barcha xodimlar |
| GET | `/reports/export` | Bitimlarni CSV faylga yuklash | Barcha xodimlar |
| GET | `/users` | Foydalanuvchilar roʻyxati | Faqat Admin |
| GET | `/users/create` | Yangi xodim qoʻshish formasi | Faqat Admin |
| POST | `/users` | Yangi xodimni roʻyxatdan oʻtkazish | Faqat Admin |
| GET | `/users/{id}/edit` | Xodim maʼlumotlarini tahrirlash | Faqat Admin |
| POST | `/users/{id}/update` | Xodim maʼlumotlarini yangilash | Faqat Admin |

---

## 🔒 Xavfsizlik Checklisti (Production Checklist)

1. [x] `.env` faylini veb orqali ochilmasligini taʼminlash (`.htaccess` va public document root orqali himoyalangan).
2. [x] Productionda `APP_DEBUG=false` qilib belgilash (stack trace lar foydalanuvchiga koʻrinmaydi, markazlashgan holda `storage/logs/app.log` ga yoziladi).
3. [x] `admin@leadpulse.uz` ning standart parolini yangilash.
4. [x] HTTPS (SSL sertifikati) yoqilgan boʻlishi.
5. [x] Cookie fayllari uchun `HttpOnly` va `SameSite=Lax` avtomatik yoqilgan.

---

## 📄 Litsenziya

MIT License. Ochiq va mustaqil foydalanish uchun moʻljallangan.
LeadPulse Mini Framework © 2026.

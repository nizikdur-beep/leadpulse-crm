<?php

// CLI Demo Seeder for LeadPulse CRM
require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

echo "=== LeadPulse Database Seeder ===\n";

$config = require __DIR__ . '/../../config/database.php';
$default = $config['default'] ?? 'mysql';
$dbConfig = $config['connections'][$default] ?? [];

try {
    $db = Database::connect($dbConfig);

    // 1. Create Admin User if not exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => 'admin@leadpulse.uz']);
    $admin = $stmt->fetch();

    if (!$admin) {
        $adminPassword = password_hash('Admin@12345', PASSWORD_DEFAULT);
        $insertAdmin = $db->prepare("
            INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
            VALUES (:name, :email, :password, 'admin', 1, datetime('now'), datetime('now'))
        ");
        $insertAdmin->execute([
            'name' => 'Bosh Administrator',
            'email' => 'admin@leadpulse.uz',
            'password' => $adminPassword
        ]);
        $adminId = (int)$db->lastInsertId();
        echo "[+] Admin yaratildi: admin@leadpulse.uz (Parol: Admin@12345)\n";
    } else {
        $adminId = (int)$admin['id'];
        echo "[i] Admin foydalanuvchisi mavjud (ID: {$adminId})\n";
    }

    // 2. Create Managers if not exist
    $managers = [
        [
            'name' => 'Aziz Rahimov',
            'email' => 'aziz@leadpulse.uz',
            'password' => password_hash('Manager@123', PASSWORD_DEFAULT),
            'role' => 'manager'
        ],
        [
            'name' => 'Nodira Salimova',
            'email' => 'nodira@leadpulse.uz',
            'password' => password_hash('Manager@123', PASSWORD_DEFAULT),
            'role' => 'manager'
        ]
    ];

    $managerIds = [];
    foreach ($managers as $mgr) {
        $stmt->execute(['email' => $mgr['email']]);
        $existing = $stmt->fetch();
        if (!$existing) {
            $ins = $db->prepare("
                INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
                VALUES (:name, :email, :password, :role, 1, datetime('now'), datetime('now'))
            ");
            $ins->execute($mgr);
            $mgrId = (int)$db->lastInsertId();
            $managerIds[] = $mgrId;
            echo "[+] Menejer yaratildi: {$mgr['email']} (Parol: Manager@123)\n";
        } else {
            $managerIds[] = (int)$existing['id'];
        }
    }

    $allAssignees = array_merge([$adminId], $managerIds);
    $mgr1 = $managerIds[0] ?? $adminId;
    $mgr2 = $managerIds[1] ?? $adminId;

    // 3. Create Sample Clients
    $sampleClients = [
        [
            'name' => 'Akbar Karimov',
            'phone' => '+998901234567',
            'email' => 'akbar@techcorp.uz',
            'company' => 'TechCorp MCHJ',
            'address' => 'Toshkent sh., Yunusobod tumani, 4-mavze',
            'notes' => 'IT yechimlari va ERP tizimlariga qiziqish bildirgan mijoz.',
            'created_by' => $adminId,
            'assigned_to' => $mgr1,
        ],
        [
            'name' => 'Malika Usmonova',
            'phone' => '+998935557788',
            'email' => 'malika@grandtextile.uz',
            'company' => 'Grand Textile XK',
            'address' => 'Samarqand sh., Registon koʻchasi, 12',
            'notes' => 'Yangi filial uchun savdo avtomatlashtirish talab qilinmoqda.',
            'created_by' => $adminId,
            'assigned_to' => $mgr2,
        ],
        [
            'name' => 'Jasur Beknazarov',
            'phone' => '+998971112233',
            'email' => 'jasur@logisticsgroup.uz',
            'company' => 'Logistics Group',
            'address' => 'Toshkent sh., Mirobod tumani',
            'notes' => 'Katta logistika xizmatlari shartnomasi boʻyicha muzokara.',
            'created_by' => $mgr1,
            'assigned_to' => $mgr1,
        ],
        [
            'name' => 'Dilshod Ergashev',
            'phone' => '+998949998877',
            'email' => 'dilshod@agrotrade.uz',
            'company' => 'AgroTrade Holding',
            'address' => 'Fargʻona sh., Sayilgoh koʻchasi',
            'notes' => 'Qishloq xoʻjaligi mahsulotlari eksporti.',
            'created_by' => $mgr2,
            'assigned_to' => $mgr2,
        ],
        [
            'name' => 'Shahnoza Aliyeva',
            'phone' => '+998912345678',
            'email' => 'shahnoza@edusoft.uz',
            'company' => 'EduSoft Academy',
            'address' => 'Toshkent sh., Chilonzor tumani',
            'notes' => 'Onlayn taʼlim platformasi litsenziyasi.',
            'created_by' => $adminId,
            'assigned_to' => $mgr1,
        ]
    ];

    $clientIds = [];
    $clientStmt = $db->prepare("SELECT id FROM clients WHERE email = :email AND deleted_at IS NULL LIMIT 1");
    $insertClient = $db->prepare("
        INSERT INTO clients (name, phone, email, company, address, notes, created_by, assigned_to, created_at, updated_at)
        VALUES (:name, :phone, :email, :company, :address, :notes, :created_by, :assigned_to, datetime('now'), datetime('now'))
    ");

    foreach ($sampleClients as $c) {
        $clientStmt->execute(['email' => $c['email']]);
        $existing = $clientStmt->fetch();
        if (!$existing) {
            $insertClient->execute($c);
            $clientIds[] = (int)$db->lastInsertId();
        } else {
            $clientIds[] = (int)$existing['id'];
        }
    }
    echo "[+] " . count($clientIds) . " ta mijoz tekshirildi/yaratildi.\n";

    // 4. Create Sample Leads in different stages
    $sampleLeads = [
        [
            'client_id' => $clientIds[0],
            'user_id' => $mgr1,
            'title' => 'CRM integratsiyasi va xodimlarni oʻqitish',
            'amount' => 45000000.00,
            'stage' => 'won',
            'source' => 'Veb-sayt',
            'notes' => 'Shartnoma imzolandi, toʻlov qabul qilindi.',
            'next_contact_at' => null,
            'lost_reason' => null
        ],
        [
            'client_id' => $clientIds[1],
            'user_id' => $mgr2,
            'title' => 'Tekstil doʻkonlari uchun kassa va ombor dasturi',
            'amount' => 32000000.00,
            'stage' => 'proposal',
            'source' => 'Tavsiya',
            'notes' => 'Tijoriy taklif yuborildi, rahbar javobi kutilmoqda.',
            'next_contact_at' => date('Y-m-d', strtotime('+2 days')),
            'lost_reason' => null
        ],
        [
            'client_id' => $clientIds[2],
            'user_id' => $mgr1,
            'title' => 'GPS monitoring va transport logistika moduli',
            'amount' => 78000000.00,
            'stage' => 'contact',
            'source' => 'Telegram reklama',
            'notes' => 'Dastlabki muzokaralar olib borilmoqda. Texnik topshiriq tayyorlanmoqda.',
            'next_contact_at' => date('Y-m-d', strtotime('+1 day')),
            'lost_reason' => null
        ],
        [
            'client_id' => $clientIds[3],
            'user_id' => $mgr2,
            'title' => 'AgroTrade e-tijorat mobil ilovasi',
            'amount' => 60000000.00,
            'stage' => 'new',
            'source' => 'Instagram',
            'notes' => 'Ilova talablari boʻyicha birinchi murojaat.',
            'next_contact_at' => date('Y-m-d'),
            'lost_reason' => null
        ],
        [
            'client_id' => $clientIds[4],
            'user_id' => $mgr1,
            'title' => 'Maktab oʻquv boshqaruvi LMS tizimi',
            'amount' => 25000000.00,
            'stage' => 'lost',
            'source' => 'Sovuq qoʻngʻiroq',
            'notes' => 'Mijoz byudjeti yetarli emas deb topildi.',
            'next_contact_at' => null,
            'lost_reason' => 'Byudjet yetarli emas'
        ]
    ];

    $leadIds = [];
    $leadCheck = $db->prepare("SELECT id FROM leads WHERE title = :title AND deleted_at IS NULL LIMIT 1");
    $insertLead = $db->prepare("
        INSERT INTO leads (client_id, user_id, title, amount, stage, source, notes, next_contact_at, lost_reason, created_at, updated_at)
        VALUES (:client_id, :user_id, :title, :amount, :stage, :source, :notes, :next_contact_at, :lost_reason, datetime('now'), datetime('now'))
    ");

    foreach ($sampleLeads as $l) {
        $leadCheck->execute(['title' => $l['title']]);
        $existing = $leadCheck->fetch();
        if (!$existing) {
            $insertLead->execute($l);
            $lid = (int)$db->lastInsertId();
            $leadIds[] = $lid;

            // Insert initial history
            $hist = $db->prepare("INSERT INTO lead_stage_history (lead_id, user_id, old_stage, new_stage, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
            $hist->execute([$lid, $l['user_id'], 'new', $l['stage']]);
        } else {
            $leadIds[] = (int)$existing['id'];
        }
    }
    echo "[+] " . count($leadIds) . " ta lead tekshirildi/yaratildi.\n";

    // 5. Create Sample Tasks
    $sampleTasks = [
        [
            'lead_id' => $leadIds[1] ?? null,
            'client_id' => $clientIds[1] ?? null,
            'user_id' => $mgr2,
            'title' => 'Tijoriy taklif boʻyicha qayta bogʻlanish',
            'description' => 'Direktor bilan uchrashuv belgilash va yakuniy narxni muhokama qilish.',
            'due_date' => date('Y-m-d', strtotime('+1 day')),
            'priority' => 'high',
            'status' => 'open'
        ],
        [
            'lead_id' => $leadIds[2] ?? null,
            'client_id' => $clientIds[2] ?? null,
            'user_id' => $mgr1,
            'title' => 'Texnik talablar va arxitekturani taqdim etish',
            'description' => 'Logistika guruhi uchun demo taqdimot tayyorlash.',
            'due_date' => date('Y-m-d'),
            'priority' => 'high',
            'status' => 'in_progress'
        ],
        [
            'lead_id' => $leadIds[3] ?? null,
            'client_id' => $clientIds[3] ?? null,
            'user_id' => $mgr2,
            'title' => 'Birlamchi qoʻngʻiroq va ehtiyojni aniqlash',
            'description' => 'AgroTrade menejeri bilan 15 daqiqalik qisqa suhbat.',
            'due_date' => date('Y-m-d', strtotime('-2 days')), // Overdue task demo
            'priority' => 'medium',
            'status' => 'open'
        ],
        [
            'lead_id' => $leadIds[0] ?? null,
            'client_id' => $clientIds[0] ?? null,
            'user_id' => $mgr1,
            'title' => 'Akt va hisob-fakturalarni rasmiylashtirish',
            'description' => 'Buxgalteriya bilan hujjatlarni imzolash.',
            'due_date' => date('Y-m-d', strtotime('-5 days')),
            'priority' => 'low',
            'status' => 'done'
        ]
    ];

    $insertTask = $db->prepare("
        INSERT INTO tasks (lead_id, client_id, user_id, title, description, due_date, priority, status, created_at, updated_at)
        VALUES (:lead_id, :client_id, :user_id, :title, :description, :due_date, :priority, :status, datetime('now'), datetime('now'))
    ");
    $taskCheck = $db->prepare("SELECT id FROM tasks WHERE title = :title LIMIT 1");

    foreach ($sampleTasks as $t) {
        $taskCheck->execute(['title' => $t['title']]);
        if (!$taskCheck->fetch()) {
            $insertTask->execute($t);
        }
    }
    echo "[+] Vazifalar tekshirildi/yaratildi.\n";

    // 6. Create Sample Activities
    $sampleActivities = [
        [
            'lead_id' => $leadIds[0] ?? null,
            'client_id' => $clientIds[0] ?? null,
            'user_id' => $mgr1,
            'type' => 'meeting',
            'note' => 'Mijoz ofisida shartnoma imzolash marosimi oʻtkazildi.'
        ],
        [
            'lead_id' => $leadIds[1] ?? null,
            'client_id' => $clientIds[1] ?? null,
            'user_id' => $mgr2,
            'type' => 'email',
            'note' => 'Tijoriy taklif va narxlar jadvali PDF formatida email orqali yuborildi.'
        ],
        [
            'lead_id' => $leadIds[2] ?? null,
            'client_id' => $clientIds[2] ?? null,
            'user_id' => $mgr1,
            'type' => 'call',
            'note' => 'Telefon orqali 20 daqiqa konsultatsiya berildi, mijoz juda qiziqmoqda.'
        ]
    ];

    $insertAct = $db->prepare("
        INSERT INTO activities (lead_id, client_id, user_id, type, note, created_at)
        VALUES (:lead_id, :client_id, :user_id, :type, :note, datetime('now'))
    ");
    $actCount = (int)$db->query("SELECT COUNT(*) FROM activities")->fetchColumn();
    if ($actCount === 0) {
        foreach ($sampleActivities as $act) {
            $insertAct->execute($act);
        }
    }
    echo "[+] Faoliyatlar tarixi tekshirildi/yaratildi.\n";

    echo "=== Demo maʼlumotlar muvaffaqiyatli kiritildi! ===\n";
} catch (\Throwable $e) {
    echo "[XATO] Seeder xatoligi: " . $e->getMessage() . "\n";
    exit(1);
}


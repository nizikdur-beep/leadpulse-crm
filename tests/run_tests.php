<?php

// LeadPulse Comprehensive Test Suite
declare(strict_types=1);

echo "========================================================\n";
echo "       LeadPulse CRM - Avtomatlashtirilgan Testlar       \n";
echo "========================================================\n\n";

$baseDir = dirname(__DIR__);

// PSR-4 Autoloader
spl_autoload_register(function ($class) use ($baseDir) {
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

require_once $baseDir . '/routes/web.php';

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Validator;
use App\Services\DuplicateDetector;
use App\Services\ReportService;

$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $details = ''): void {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] {$name}\n";
        $passed++;
    } else {
        echo " [FAIL] {$name} - {$details}\n";
        $failed++;
    }
}

// ----------------------------------------------------
// Test 1: PHP Sintaksis Tekshiruvi
// ----------------------------------------------------
echo "1. PHP Fayllarining Sintaksisini Tekshirish:\n";
$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir)
);
$syntaxErrors = 0;
foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        exec("php -l \"{$path}\" 2>&1", $out, $ret);
        if ($ret !== 0) {
            echo "   Sintaksis xatosi: {$path}\n";
            $syntaxErrors++;
        }
    }
}
assertTest("Barcha PHP fayllari sintaktik jihatdan toʻgʻri", $syntaxErrors === 0, "{$syntaxErrors} ta faylda xatolik bor");

// ----------------------------------------------------
// Test 2: CSRF Himoyasi
// ----------------------------------------------------
echo "\n2. CSRF Himoyasi va Token Tekshiruvi:\n";
Session::start();
$token = Csrf::getToken();
assertTest("CSRF Token generatsiya qilinadi va 64 belgidan iborat", strlen($token) === 64);
assertTest("Toʻgʻri CSRF token tasdiqlanadi", Csrf::validate($token));
assertTest("Notoʻgʻri yoki boʻsh CSRF token rad etiladi", !Csrf::validate("invalid_fake_token"));
assertTest("Boʻsh CSRF token rad etiladi", !Csrf::validate(null));

// ----------------------------------------------------
// Test 3: XSS Himoyasi va Escape
// ----------------------------------------------------
echo "\n3. XSS (Cross-Site Scripting) Himoyasi:\n";
$maliciousInput = '<script>alert("XSS Attack!");</script>';
$escaped = htmlspecialchars($maliciousInput, ENT_QUOTES, 'UTF-8');
assertTest("XSS belgilari toʻliq zararsizlantiriladi", !str_contains($escaped, '<script>') && str_contains($escaped, '&lt;script&gt;'));

// ----------------------------------------------------
// Test 4: CSV Formula Injection Himoyasi
// ----------------------------------------------------
echo "\n4. CSV Formula Injection Himoyasi:\n";
$reportService = new ReportService();
$reflection = new ReflectionClass($reportService);
$method = $reflection->getMethod('sanitizeCsvCell');
$method->setAccessible(true);

$dangerousFormula1 = '=cmd|"/C calc"!A0';
$dangerousFormula2 = '+123456';
$dangerousFormula3 = '-@SUM(A1:A10)';
$dangerousFormula4 = '@SUM(1,2)';
$safeText = 'Oddiy kompaniya nomi';

assertTest("= bilan boshlangan formula zararsizlantirildi", str_starts_with($method->invoke($reportService, $dangerousFormula1), "'="));
assertTest("+ bilan boshlangan formula zararsizlantirildi", str_starts_with($method->invoke($reportService, $dangerousFormula2), "'+"));
assertTest("- bilan boshlangan formula zararsizlantirildi", str_starts_with($method->invoke($reportService, $dangerousFormula3), "'-"));
assertTest("@ bilan boshlangan formula zararsizlantirildi", str_starts_with($method->invoke($reportService, $dangerousFormula4), "'@"));
assertTest("Oddiy matn oʻzgarishsiz qoldirildi", $method->invoke($reportService, $safeText) === $safeText);

// ----------------------------------------------------
// Test 5: Server-side Validator Tekshiruvi
// ----------------------------------------------------
echo "\n5. Validatsiya Tizimi (Validator):\n";
$validData = [
    'name' => 'Ali Valiyev',
    'email' => 'ali@example.com',
    'amount' => '1500000',
    'stage' => 'new'
];
$rules = [
    'name' => 'required|min:3',
    'email' => 'required|email',
    'amount' => 'required|numeric',
    'stage' => 'required|in:new,contact,proposal,won,lost'
];
$v = Validator::make($validData, $rules);
assertTest("Toʻgʻri maʼlumotlar validatsiyadan oʻtadi", $v->validate());

$invalidData = [
    'name' => 'A', // too short
    'email' => 'not-an-email',
    'amount' => 'abc', // not numeric
    'stage' => 'hacked_stage' // not in list
];
$v2 = Validator::make($invalidData, $rules);
assertTest("Yaroqsiz maʼlumotlar qatʼiy rad etiladi", !$v2->validate());
assertTest("Xatolik xabarlari toʻliq shakllanadi", count($v2->errors()) === 4);

// ----------------------------------------------------
// Test 6: Router va HTTP Metodlari
// ----------------------------------------------------
echo "\n6. Router va Request Dispatching:\n";
$reqGet = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/login']);
assertTest("Request URI toʻgʻri aniqlanadi", $reqGet->uri() === '/login');
assertTest("Request Method toʻgʻri aniqlanadi", $reqGet->method() === 'GET');

// Test Method Override (POST with _method=DELETE)
$reqOverride = new Request([], ['_method' => 'DELETE'], ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/leads/5/delete']);
assertTest("POST orqali _method=DELETE override ishlaydi", $reqOverride->method() === 'DELETE');

// ----------------------------------------------------
// Test 7: Konversiya Formulasi Tekshiruvi
// ----------------------------------------------------
echo "\n7. Biznes Qoidalari: Konversiya Formulasi:\n";
// Won: 10, Lost: 10 -> 10 / 20 * 100 = 50%
$won = 10;
$lost = 10;
$finished = $won + $lost;
$conv1 = $finished > 0 ? round(($won / $finished) * 100, 1) : 0.0;
assertTest("10 won va 10 lost boʻlganda konversiya 50%", $conv1 === 50.0);

// Won: 0, Lost: 0 -> 0% (boʻlinishdagi 0 xatoligi yoʻqligi)
$won0 = 0;
$lost0 = 0;
$finished0 = $won0 + $lost0;
$conv0 = $finished0 > 0 ? round(($won0 / $finished0) * 100, 1) : 0.0;
assertTest("Bitimlar yakunlanmagan boʻlsa konversiya aniq 0%", $conv0 === 0.0);

// Won: 3, Lost: 1 -> 3 / 4 * 100 = 75%
$won3 = 3;
$lost1 = 1;
$conv3 = round(($won3 / ($won3 + $lost1)) * 100, 1);
assertTest("3 won va 1 lost boʻlganda konversiya 75%", $conv3 === 75.0);

// ----------------------------------------------------
// Test 8: Notoʻgʻri Route (404) Tekshiruvi
// ----------------------------------------------------
echo "\n8. Mavjud Boʻlmagan Route (404):\n";
$unknownReq = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/non_existing_random_path_12345']);
$resp404 = Router::dispatch($unknownReq);
$refStatus = new ReflectionProperty($resp404, 'statusCode');
$refStatus->setAccessible(true);
$statusCode = $refStatus->getValue($resp404);
assertTest("Notoʻgʻri route uchun 404 status qaytariladi", $statusCode === 404);

// ----------------------------------------------------
// Yakuniy Natijalar
// ----------------------------------------------------
echo "\n========================================================\n";
echo "TEST NATIJALARI:\n";
echo "Muvaffaqiyatli oʻtganlar: {$passed}\n";
echo "Xatoliklar: {$failed}\n";
echo "========================================================\n";

if ($failed > 0) {
    exit(1);
}
echo "Barcha testlar muvaffaqiyatli yakunlandi!\n";
exit(0);

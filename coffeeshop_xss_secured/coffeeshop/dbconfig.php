<?php
// Database connection using PDO

// ---- XSS DEMO SWITCH ----
// false = VULNERABLE (for demonstrating the XSS attack only)
// true  = SECURE (escaping, input checks, safe cookie, security headers, strong passwords)
define('SECURE_MODE', true);
define('BASE_URL', '/coffeeshop');   // change if your folder name is different

$host    = 'localhost';
$dbname  = 'coffeeshop';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Connection failed: ' . htmlspecialchars($e->getMessage()));
}

// ---- XSS protection: safer session cookie + security headers ----
if (SECURE_MODE) {
    ini_set('session.cookie_httponly', 1);   // JavaScript cannot read the session cookie
    ini_set('session.use_strict_mode', 1);
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output so the browser shows it as text and never runs it as code
function e($v) {
    if (!SECURE_MODE) { return (string)$v; }   // vulnerable on purpose (demo only)
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Input check: true if the text contains < or > (possible HTML / script tags)
function has_html($text) {
    return (bool)preg_match('/[<>]/', (string)$text);
}

// Password rules: min 8 characters, with uppercase, lowercase and a number.
// Returns true when OK, or an error message string when not.
function is_strong_password($password) {
    if (strlen($password) < 8)              return 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $password))  return 'Password must have at least one uppercase letter.';
    if (!preg_match('/[a-z]/', $password))  return 'Password must have at least one lowercase letter.';
    if (!preg_match('/[0-9]/', $password))  return 'Password must have at least one number.';
    return true;
}

function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (isset($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function redirect($path) { header('Location: ' . BASE_URL . $path); exit; }

// ---- AUTH HELPERS ----

// Is anyone logged in right now?
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// The id of the logged-in user, or null
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// The username of the logged-in user
function current_username() {
    return $_SESSION['username'] ?? '';
}

// Call this at the top of any page that requires login.
// If nobody is logged in, bounce to the login page.
function require_login() {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

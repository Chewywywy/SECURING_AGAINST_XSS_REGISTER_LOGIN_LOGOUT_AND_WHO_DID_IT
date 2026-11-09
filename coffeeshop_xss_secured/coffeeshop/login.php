<?php
$title = 'Login';
require_once 'dbconfig.php';

if (is_logged_in()) { redirect('/index.php'); }

$username = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $u = $stmt->fetch();

    if ($u && password_verify($password, $u['password'])) {
        // regenerate session id on login to prevent session fixation
        session_regenerate_id(true);
        $_SESSION['user_id']  = $u['user_id'];
        $_SESSION['username'] = $u['username'];
        flash('Welcome back, ' . $u['first_name'] . '!');
        redirect('/index.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Bean Counter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<header class="top">
  <a class="brand" href="<?= BASE_URL ?>/login.php">Bean Counter</a>
</header>
<main>
<?php $f = flash(); if ($f): ?><div class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
<div class="head"><div><h1>Log in</h1><p>Enter your credentials to access the menu system.</p></div></div>
<?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <label for="username">Username</label>
  <input id="username" name="username" value="<?= e($username) ?>" required autofocus>
  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>
  <div class="actions"><button class="btn" type="submit">Log in</button></div>
</form>
<p style="margin-top:1rem">No account yet? <a href="register.php">Register</a></p>
</main>
</body>
</html>

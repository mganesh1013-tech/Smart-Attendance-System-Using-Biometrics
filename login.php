<?php
session_start();
if (!empty($_SESSION["user_id"])) { header("Location: dashboard.php"); exit; }
require_once "config/database.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $u = trim($_POST["username"] ?? "");
    $p = $_POST["password"] ?? "";
    $st = $pdo->prepare("SELECT id,username,password,role FROM users WHERE username=? LIMIT 1");
    $st->execute([$u]); $user = $st->fetch();
    if ($user && password_verify($p, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"]=$user["id"]; $_SESSION["username"]=$user["username"]; $_SESSION["role"]=$user["role"];
        header("Location: dashboard.php"); exit;
    }
    $error = "Invalid username or password.";
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | Smart Attendance</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="login-body"><div class="login-card">
<div class="brand login-brand"><div class="brand-icon">✓</div><div><strong>Smart Attend</strong><small>Biometric Attendance System</small></div></div>
<h1>Welcome back</h1><p class="muted">Sign in to manage your college attendance.</p>
<?php if($error): ?><div class="alert danger"><?=h($error)?></div><?php endif; ?>
<form method="post"><label>Username</label><input name="username" value="admin" required>
<label>Password</label><input type="password" name="password" value="admin123" required>
<button class="btn primary full">Sign In</button></form>
<p class="demo-note">Initial administrator: <b>admin</b> / <b>admin123</b></p>
</div></body></html>
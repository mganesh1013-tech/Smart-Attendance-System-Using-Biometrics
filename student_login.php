<?php
session_start();
require_once "config/database.php";

if (isset($_SESSION["student_id"])) {
    header("Location: student_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $st = $pdo->prepare("
            SELECT id, student_id, name, username, password, status
            FROM students
            WHERE username = ?
            LIMIT 1
        ");
        $st->execute([$username]);
        $student = $st->fetch();

        if ($student && $student["status"] === "Active" &&
            password_verify($password, $student["password"])) {

            $_SESSION["student_id"] = (int)$student["id"];
            $_SESSION["student_code"] = $student["student_id"];
            $_SESSION["student_name"] = $student["name"];

            header("Location: student_dashboard.php");
            exit;
        }

        $error = "Invalid student username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login</title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f4f6fb;display:flex;align-items:center;justify-content:center;min-height:100vh}
.card{width:360px;background:#fff;padding:30px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
h1{margin-top:0}.sub{color:#666;margin-bottom:22px}
label{display:block;margin:14px 0 6px;font-weight:bold}
input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #ddd;border-radius:8px}
button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:8px;background:#222;color:#fff;font-size:16px;cursor:pointer}
.error{background:#ffe7e7;color:#a00;padding:10px;border-radius:8px;margin-bottom:15px}
</style>
</head>
<body>
<div class="card">
    <h1>Student Login</h1>
    <div class="sub">Smart Attendance System</div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Username</label>
        <input name="username" autocomplete="username" required>

        <label>Password</label>
        <input name="password" type="password" autocomplete="current-password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

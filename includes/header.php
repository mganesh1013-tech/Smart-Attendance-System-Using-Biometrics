<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? "Dashboard";
$current = basename($_SERVER["PHP_SELF"]);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($pageTitle) ?> | Smart Attendance</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
<aside class="sidebar">
  <div class="brand"><div class="brand-icon">✓</div><div><strong>Smart Attend</strong><small>Biometric System</small></div></div>
  <nav>
    <a class="<?= $current==='dashboard.php'?'active':'' ?>" href="dashboard.php">▦ <span>Dashboard</span></a>
    <a class="<?= $current==='students.php'?'active':'' ?>" href="students.php">♙ <span>Students</span></a>
    <a class="<?= $current==='subjects.php'?'active':'' ?>" href="subjects.php">▤ <span>Subjects</span></a>
    <a class="<?= $current==='faculty.php'?'active':'' ?>" href="faculty.php">♙ <span>Faculty</span></a>
    <a class="<?= $current==='attendance.php'?'active':'' ?>" href="attendance.php">◷ <span>Attendance</span></a>
    <a class="<?= $current==='reports.php'?'active':'' ?>" href="reports.php">▥ <span>Reports</span></a>
    <a class="<?= $current==='parent_alerts.php'?'active':'' ?>" href="parent_alerts.php">◉ <span>Parent Alerts</span></a>
    <a class="<?= $current==='devices.php'?'active':'' ?>" href="devices.php">⌁ <span>Devices</span></a>
  </nav>
  <div class="sidebar-bottom">
    <div class="user-mini">● <?= h($_SESSION["username"] ?? "Admin") ?><small><?= h($_SESSION["role"] ?? "Admin") ?></small></div>
    <a href="logout.php">↪ <span>Logout</span></a>
  </div>
</aside>
<main class="main">
<header class="topbar">
  <div><h1><?= h($pageTitle) ?></h1><p><?= date("l, d M Y") ?></p></div>
  <div class="top-user">Administrator</div>
</header>
<section class="content">

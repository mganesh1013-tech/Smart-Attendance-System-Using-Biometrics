<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

$studentDbId = (int)$_SESSION["student_id"];

$st = $pdo->prepare("
    SELECT *
    FROM students
    WHERE id = ? AND status = 'Active'
    LIMIT 1
");
$st->execute([$studentDbId]);
$student = $st->fetch();

if (!$student) {
    session_destroy();
    header("Location: student_login.php");
    exit;
}

$totalStmt = $pdo->prepare("
    SELECT COUNT(*) FROM attendance
    WHERE student_id = ?
");
$totalStmt->execute([$studentDbId]);
$total = (int)$totalStmt->fetchColumn();

$presentStmt = $pdo->prepare("
    SELECT COUNT(*) FROM attendance
    WHERE student_id = ? AND status = 'Present'
");
$presentStmt->execute([$studentDbId]);
$present = (int)$presentStmt->fetchColumn();

$absent = max(0, $total - $present);
$percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

$rowsStmt = $pdo->prepare("
    SELECT
        a.*,
        COALESCE(sub.name, 'All Subjects') AS subject_name,
        COALESCE(sub.code, '') AS subject_code
    FROM attendance a
    LEFT JOIN subjects sub ON sub.id = a.subject_id
    WHERE a.student_id = ?
    ORDER BY a.date DESC, a.time DESC, a.id DESC
    LIMIT 100
");
$rowsStmt->execute([$studentDbId]);
$rows = $rowsStmt->fetchAll();

function h2($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f4f6fb;color:#222}
.top{background:#fff;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.logout{background:#222;color:#fff;text-decoration:none;padding:9px 14px;border-radius:7px}
.wrap{max-width:1100px;margin:30px auto;padding:0 18px}
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.05)}
.card small{display:block;color:#777;margin-bottom:8px}.card strong{font-size:28px}
.panel{background:#fff;margin-top:20px;padding:22px;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.05)}
table{width:100%;border-collapse:collapse}th,td{padding:12px;border-bottom:1px solid #eee;text-align:left}
.badge{padding:5px 9px;border-radius:15px;font-size:12px}
.present{background:#dff7e7;color:#18743a}.absent{background:#ffe0e0;color:#a11}
.warning{background:#fff0c9;color:#8a6100}
.progress{height:10px;background:#eee;border-radius:10px;overflow:hidden;margin-top:12px}
.progress div{height:100%;background:#222}
@media(max-width:700px){.cards{grid-template-columns:repeat(2,1fr)}.top{padding:15px}.wrap{padding:0 12px}}
</style>
</head>
<body>
<div class="top">
    <div>
        <strong>Smart Attendance System</strong><br>
        <small>Student Portal</small>
    </div>
    <a class="logout" href="student_logout.php">Logout</a>
</div>

<div class="wrap">
    <h1>Welcome, <?= h2($student["name"]) ?></h1>
    <p>Student ID: <strong><?= h2($student["student_id"]) ?></strong></p>

    <div class="cards">
        <div class="card"><small>Total Classes</small><strong><?= $total ?></strong></div>
        <div class="card"><small>Present</small><strong><?= $present ?></strong></div>
        <div class="card"><small>Absent</small><strong><?= $absent ?></strong></div>
        <div class="card"><small>Attendance</small><strong><?= $percentage ?>%</strong></div>
    </div>

    <div class="panel">
        <h2>Attendance Percentage</h2>
        <div class="progress">
            <div style="width:<?= min(100, max(0, $percentage)) ?>%"></div>
        </div>
        <?php if ($percentage < 75): ?>
            <p class="warning">⚠ Your attendance is below 75%.</p>
        <?php else: ?>
            <p class="present">✓ Your attendance is 75% or above.</p>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>My Attendance</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Source</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= h2($r["subject_name"]) ?></td>
                    <td><?= h2($r["date"]) ?></td>
                    <td><?= h2($r["time"]) ?></td>
                    <td><?= h2($r["source"]) ?></td>
                    <td>
                        <?php $cls = strtolower($r["status"]) === "present" ? "present" : "absent"; ?>
                        <span class="badge <?= $cls ?>"><?= h2($r["status"]) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$rows): ?>
                <tr><td colspan="5">No attendance records yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

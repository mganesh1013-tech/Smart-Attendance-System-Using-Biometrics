<?php
require_once "includes/auth.php"; require_login(); require_once "config/database.php";
$pageTitle="Dashboard"; $today=date("Y-m-d");
$total=(int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='Active'")->fetchColumn();
$present=(int)$pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE date=? AND status='Present'")->execute([$today]) ? $pdo->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE date='$today' AND status='Present'")->fetchColumn() : 0;
$absent = max(0,$total-$present);
$rate = $total ? round($present/$total*100,1) : 0;
$low=(int)$pdo->query("SELECT COUNT(*) FROM (
 SELECT s.id, COALESCE(SUM(a.status='Present')/NULLIF(COUNT(a.id),0)*100,0) pct
 FROM students s LEFT JOIN attendance a ON a.student_id=s.id
 WHERE s.status='Active' GROUP BY s.id HAVING pct < 75
) x")->fetchColumn();
$recent=$pdo->query("SELECT a.*,s.student_id,s.name,COALESCE(sub.name,'All Subjects') subject_name FROM attendance a JOIN students s ON s.id=a.student_id LEFT JOIN subjects sub ON sub.id=a.subject_id ORDER BY a.id DESC LIMIT 10")->fetchAll();
$days=$pdo->query("SELECT date, SUM(status='Present') present_count, SUM(status='Absent') absent_count FROM attendance GROUP BY date ORDER BY date DESC LIMIT 7")->fetchAll();
require "includes/header.php";
?>
<div class="cards">
<div class="stat"><span>♙</span><div><small>Total Students</small><strong><?=$total?></strong></div></div>
<div class="stat"><span>✓</span><div><small>Present Today</small><strong><?=$present?></strong></div></div>
<div class="stat"><span>×</span><div><small>Absent Today</small><strong><?=$absent?></strong></div></div>
<div class="stat"><span>!</span><div><small>Below 75%</small><strong><?=$low?></strong></div></div>
</div>
<div class="grid-2">
<section class="panel"><div class="panel-head"><div><h2>Today's Overview</h2><p>Attendance rate: <b><?=$rate?>%</b></p></div><a class="btn ghost" href="attendance.php">View attendance</a></div>
<div class="progress"><div style="width:<?=$rate?>%"></div></div>
<div class="mini-stats"><div><b><?=$present?></b><small>Present</small></div><div><b><?=$absent?></b><small>Absent</small></div><div><b><?=$rate?>%</b><small>Rate</small></div></div>
</section>
<section class="panel"><div class="panel-head"><div><h2>Device Status</h2><p>ESP32 biometric gateway</p></div></div>
<?php $devs=$pdo->query("SELECT * FROM devices ORDER BY id")->fetchAll(); foreach($devs as $d): ?>
<div class="status-row"><span><i class="dot <?=strtolower($d["status"])==="online"?"green":"orange"?>"></i><?=h($d["device_name"])?></span><b><?=h($d["status"])?></b></div>
<?php endforeach; if(!$devs): ?><div class="empty">No device registered.</div><?php endif; ?>
</section>
</div>
<section class="panel"><div class="panel-head"><div><h2>Recent Attendance</h2><p>Latest biometric and manual records</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Student</th><th>Subject</th><th>Date</th><th>Time</th><th>Source</th><th>Status</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><b><?=h($r["name"])?></b><small class="sub"><?=h($r["student_id"])?></small></td><td><?=h($r["subject_name"])?></td><td><?=h($r["date"])?></td><td><?=h($r["time"])?></td><td><?=h($r["source"])?></td><td><span class="badge <?=strtolower($r["status"])?>"><?=h($r["status"])?></span></td></tr>
<?php endforeach; if(!$recent): ?><tr><td colspan="6" class="empty">No attendance records yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require "includes/footer.php"; ?>
<?php
require_once "includes/auth.php"; require_login(); require_once "config/database.php"; $pageTitle="Attendance";
$date=$_GET["date"]??date("Y-m-d"); $subject=$_GET["subject"]??"";
$sql="SELECT a.*,s.student_id,s.name,COALESCE(sub.name,'All Subjects') subject_name FROM attendance a JOIN students s ON s.id=a.student_id LEFT JOIN subjects sub ON sub.id=a.subject_id WHERE a.date=?";
$params=[$date]; if($subject!==""){ $sql.=" AND a.subject_id=?"; $params[]=(int)$subject; } $sql.=" ORDER BY a.time DESC";
$st=$pdo->prepare($sql);$st->execute($params);$rows=$st->fetchAll();$subjects=$pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
require "includes/header.php";?>
<section class="panel"><div class="panel-head"><div><h2>Attendance Records</h2><p>Real records received from biometric devices appear here.</p></div></div>
<form class="filters" method="get"><div><label>Date</label><input type="date" name="date" value="<?=h($date)?>"></div><div><label>Subject</label><select name="subject"><option value="">All subjects</option><?php foreach($subjects as $s):?><option value="<?=$s["id"]?>" <?=$subject==$s["id"]?"selected":""?>><?=h($s["name"])?></option><?php endforeach;?></select></div><button class="btn primary">Filter</button></form>
<div class="table-wrap"><table><thead><tr><th>Student</th><th>Subject</th><th>Date</th><th>Time</th><th>Source</th><th>Status</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><b><?=h($r["name"])?></b><small class="sub"><?=h($r["student_id"])?></small></td><td><?=h($r["subject_name"])?></td><td><?=h($r["date"])?></td><td><?=h($r["time"])?></td><td><?=h($r["source"])?></td><td><span class="badge <?=strtolower($r["status"])?>"><?=h($r["status"])?></span></td></tr><?php endforeach;if(!$rows):?><tr><td colspan="6" class="empty">No records for this date.</td></tr><?php endif;?></tbody></table></div></section>
<?php require "includes/footer.php"; ?>
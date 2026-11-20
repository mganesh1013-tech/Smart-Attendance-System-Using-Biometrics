<?php
require_once "includes/auth.php"; require_login(); require_once "config/database.php"; $pageTitle="Devices";
$rows=$pdo->query("SELECT * FROM devices ORDER BY id")->fetchAll(); require "includes/header.php";?>
<section class="panel"><div class="panel-head"><div><h2>Biometric Devices</h2><p>ESP32 gateways connected to the attendance server.</p></div></div>
<div class="device-grid"><?php foreach($rows as $d):?><div class="device-card"><div class="device-icon">⌁</div><h3><?=h($d["device_name"])?></h3><p><?=h($d["device_id"])?></p><span class="badge <?=strtolower($d["status"])==="online"?"present":"warning"?>"><?=h($d["status"])?></span><small>Last seen: <?=h($d["last_seen"]??"Never")?></small></div><?php endforeach;?></div>
<div class="info-box"><b>API endpoint:</b> <code>/smart_attendance/api/attendance.php</code><br>ESP32 sends <code>fingerprint_id</code>, <code>device_id</code>, and optional <code>subject_id</code>.</div></section>
<?php require "includes/footer.php"; ?>
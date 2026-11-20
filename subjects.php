<?php
require_once "includes/auth.php"; require_login(); require_once "config/database.php"; $pageTitle="Subjects"; $msg=""; $err="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 try{
  if(($_POST["action"]??"")==="add"){ $st=$pdo->prepare("INSERT INTO subjects(name,code) VALUES(?,?)"); $st->execute([trim($_POST["name"]),trim($_POST["code"])]); $msg="Subject added."; }
  if(($_POST["action"]??"")==="delete"){ $pdo->prepare("DELETE FROM subjects WHERE id=?")->execute([(int)$_POST["id"]]); $msg="Subject deleted."; }
 }catch(Exception $e){$err=$e->getMessage();}
}
$rows=$pdo->query("SELECT sub.*,COUNT(a.id) attendance_count FROM subjects sub LEFT JOIN attendance a ON a.subject_id=sub.id GROUP BY sub.id ORDER BY sub.name")->fetchAll();
require "includes/header.php"; ?>
<?php if($msg):?><div class="alert success"><?=h($msg)?></div><?php endif;?><?php if($err):?><div class="alert danger"><?=h($err)?></div><?php endif;?>
<section class="panel"><div class="panel-head"><div><h2>Subjects</h2><p>Subjects used for attendance sessions.</p></div><button class="btn primary" onclick="toggleModal('subjectModal')">+ Add Subject</button></div>
<div class="table-wrap"><table><thead><tr><th>Code</th><th>Subject</th><th>Attendance Records</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><span class="fp"><?=h($r["code"])?></span></td><td><b><?=h($r["name"])?></b></td><td><?=h($r["attendance_count"])?></td><td><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r["id"]?>"><button class="icon-btn">🗑</button></form></td></tr><?php endforeach;?>
</tbody></table></div></section>
<div class="modal" id="subjectModal"><div class="modal-card small"><div class="panel-head"><h2>Add Subject</h2><button class="close" onclick="toggleModal('subjectModal')">×</button></div><form method="post"><input type="hidden" name="action" value="add"><label>Subject Name</label><input name="name" required><label>Subject Code</label><input name="code" required><button class="btn primary full">Save Subject</button></form></div></div>
<?php require "includes/footer.php"; ?>
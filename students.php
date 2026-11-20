<?php
require_once "includes/auth.php"; require_login(); require_once "config/database.php"; $pageTitle="Students"; $msg=""; $err="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $action=$_POST["action"]??"";
 try{
  if($action==="add"){
   $st=$pdo->prepare("INSERT INTO students(student_id,name,department,year,section,roll_number,fingerprint_id,parent_name,parent_email,parent_phone,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
   $st->execute([trim($_POST["student_id"]),trim($_POST["name"]),trim($_POST["department"]),trim($_POST["year"]),trim($_POST["section"]),trim($_POST["roll_number"]),trim($_POST["fingerprint_id"]),trim($_POST["parent_name"]),trim($_POST["parent_email"]),trim($_POST["parent_phone"]),"Active"]); $msg="Student added.";
  } elseif($action==="delete"){
   $pdo->prepare("DELETE FROM students WHERE id=?")->execute([(int)$_POST["id"]]); $msg="Student deleted.";
  }
 }catch(Exception $e){$err=$e->getMessage();}
}
$students=$pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
require "includes/header.php";
?>
<?php if($msg):?><div class="alert success"><?=h($msg)?></div><?php endif;?><?php if($err):?><div class="alert danger">Could not save: <?=h($err)?></div><?php endif;?>
<section class="panel"><div class="panel-head"><div><h2>Student Management</h2><p>Register students and link their fingerprint IDs.</p></div><button class="btn primary" onclick="toggleModal('studentModal')">+ Add Student</button></div>
<div class="toolbar"><input id="studentSearch" placeholder="Search by ID, name, department..." onkeyup="filterTable('studentSearch','studentsTable')"></div>
<div class="table-wrap"><table id="studentsTable"><thead><tr><th>Student</th><th>Department</th><th>Year/Section</th><th>Fingerprint</th><th>Parent Contact</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($students as $s):?><tr><td><b><?=h($s["name"])?></b><small class="sub"><?=h($s["student_id"])?></small></td><td><?=h($s["department"])?></td><td><?=h($s["year"]." / ".$s["section"])?></td><td><span class="fp">#<?=h($s["fingerprint_id"])?></span></td><td><?=h($s["parent_email"] ?: $s["parent_phone"])?></td><td><span class="badge present"><?=h($s["status"])?></span></td><td><form method="post" onsubmit="return confirm('Delete this student?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$s["id"]?>"><button class="icon-btn">🗑</button></form></td></tr>
<?php endforeach;if(!$students):?><tr><td colspan="7" class="empty">No students registered.</td></tr><?php endif;?></tbody></table></div></section>
<div class="modal" id="studentModal"><div class="modal-card"><div class="panel-head"><div><h2>Add Student</h2><p>All fields can be edited later in the database.</p></div><button class="close" onclick="toggleModal('studentModal')">×</button></div>
<form method="post" class="form-grid"><input type="hidden" name="action" value="add">
<div><label>Student ID *</label><input name="student_id" required></div><div><label>Full Name *</label><input name="name" required></div>
<div><label>Department</label><input name="department" placeholder="ECE"></div><div><label>Year</label><input name="year" placeholder="3"></div>
<div><label>Section</label><input name="section" placeholder="A"></div><div><label>Roll Number</label><input name="roll_number"></div>
<div><label>Fingerprint ID *</label><input name="fingerprint_id" type="number" min="1" required></div><div><label>Parent Name</label><input name="parent_name"></div>
<div><label>Parent Email</label><input name="parent_email" type="email"></div><div><label>Parent Phone</label><input name="parent_phone"></div>
<div class="form-actions"><button type="button" class="btn ghost" onclick="toggleModal('studentModal')">Cancel</button><button class="btn primary">Save Student</button></div></form></div></div>
<?php require "includes/footer.php"; ?>
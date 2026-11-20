<?php
header("Content-Type: application/json");
require_once "../config/database.php";
if($_SERVER["REQUEST_METHOD"]!=="POST"){http_response_code(405);echo json_encode(["success"=>false,"message"=>"POST required"]);exit;}
$data=json_decode(file_get_contents("php://input"),true);if(!is_array($data))$data=$_POST;
$fp=trim((string)($data["fingerprint_id"]??""));$device=trim((string)($data["device_id"]??"ESP32_01"));$subject=(int)($data["subject_id"]??0);
if($fp===""){http_response_code(400);echo json_encode(["success"=>false,"message"=>"fingerprint_id required"]);exit;}
$st=$pdo->prepare("SELECT * FROM students WHERE fingerprint_id=? AND status='Active' LIMIT 1");$st->execute([$fp]);$s=$st->fetch();
if(!$s){http_response_code(404);echo json_encode(["success"=>false,"message"=>"Student not found"]);exit;}
$date=date("Y-m-d");$time=date("H:i:s");
$check=$pdo->prepare("SELECT id FROM attendance WHERE student_id=? AND date=? AND ((subject_id=? ) OR (?=0 AND subject_id IS NULL)) LIMIT 1");$check->execute([$s["id"],$date,$subject,$subject]);
if($check->fetch()){echo json_encode(["success"=>false,"message"=>"Attendance already marked","student_name"=>$s["name"],"status"=>"Already Marked"]);exit;}
$ins=$pdo->prepare("INSERT INTO attendance(student_id,subject_id,date,time,status,source,device_id) VALUES(?,?,?,?,?,?,?)");$ins->execute([$s["id"],$subject?:null,$date,$time,"Present","Biometric",$device]);
$pdo->prepare("UPDATE devices SET status='Online',last_seen=NOW() WHERE device_id=?")->execute([$device]);
echo json_encode(["success"=>true,"message"=>"Attendance marked successfully","student_id"=>$s["student_id"],"student_name"=>$s["name"],"status"=>"Present","date"=>$date,"time"=>$time]);
?>
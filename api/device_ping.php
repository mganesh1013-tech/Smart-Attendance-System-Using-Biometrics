<?php
header("Content-Type: application/json"); require_once "../config/database.php";
$data=json_decode(file_get_contents("php://input"),true);if(!is_array($data))$data=$_POST;
$id=trim((string)($data["device_id"]??""));$name=trim((string)($data["device_name"]??"Attendance ESP32"));
if($id===""){http_response_code(400);echo json_encode(["success"=>false,"message"=>"device_id required"]);exit;}
$st=$pdo->prepare("INSERT INTO devices(device_id,device_name,status,last_seen) VALUES(?,?, 'Online',NOW()) ON DUPLICATE KEY UPDATE device_name=VALUES(device_name),status='Online',last_seen=NOW()");
$st->execute([$id,$name]);echo json_encode(["success"=>true,"message"=>"Device online"]);
?>
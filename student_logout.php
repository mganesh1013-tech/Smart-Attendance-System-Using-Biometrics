<?php
session_start();
unset($_SESSION["student_id"], $_SESSION["student_code"], $_SESSION["student_name"]);
header("Location: student_login.php");
exit;

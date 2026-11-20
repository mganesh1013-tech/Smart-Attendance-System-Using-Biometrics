<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendParentEmail($parentEmail, $studentName, $date, $type = "Absence")
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'YOUR_GMAIL_ADDRESS';
        $mail->Password   = 'YOUR_APP_PASSWORD';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('mganeshkmr23@gmail.com', 'Smart Attendance System');
        $mail->addAddress($parentEmail);
        $mail->isHTML(true);

        if ($type === "Absence") {
            $mail->Subject = "Attendance Alert - $studentName";
            $mail->Body = "
                <h2>Attendance Alert</h2>
                <p>Dear Parent,</p>
                <p>Your ward <strong>" . htmlspecialchars($studentName) . "</strong> is marked <strong>absent</strong> today (<strong>" . htmlspecialchars($date) . "</strong>).</p>
                <p>Please take note of the attendance.</p>
                <br>
                <p>Regards,<br><strong>Smart Attendance System</strong></p>
            ";
        } else {
            $mail->Subject = "Low Attendance Alert - $studentName";
            $mail->Body = "
                <h2>Low Attendance Alert</h2>
                <p>Dear Parent,</p>
                <p>Your ward <strong>" . htmlspecialchars($studentName) . "</strong> has attendance below 75%.</p>
                <p>Please take necessary action.</p>
                <br>
                <p>Regards,<br><strong>Smart Attendance System</strong></p>
            ";
        }

        $mail->AltBody = "Dear Parent,\n\nYour ward $studentName has an attendance alert on $date.\n\nRegards,\nSmart Attendance System";
        $mail->send();

        return [
            "success" => true,
            "message" => "Email sent successfully"
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => $mail->ErrorInfo
        ];
    }
}
?>

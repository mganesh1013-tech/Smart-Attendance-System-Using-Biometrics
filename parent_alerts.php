<?php
require_once "includes/auth.php";
require_login();

require_once "config/database.php";
require_once "config/email.php";

$pageTitle = "Parent Alerts";
$msg = "";
$msgType = "success";
$today = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "generate_alerts") {

    $students = $pdo->query("
        SELECT * FROM students
        WHERE status = 'Active'
        ORDER BY name
    ")->fetchAll();

    $created = 0;
    $sent = 0;
    $failed = 0;

    foreach ($students as $student) {

        $studentDbId = (int)$student["id"];
        $studentName = $student["name"];
        $parentEmail = trim($student["parent_email"] ?? "");

        // Check whether the student is present today.
        $presentStmt = $pdo->prepare("
            SELECT COUNT(*) FROM attendance
            WHERE student_id = ? AND date = ? AND status = 'Present'
        ");
        $presentStmt->execute([$studentDbId, $today]);
        $isPresent = ((int)$presentStmt->fetchColumn() > 0);

        // ---------- ABSENCE ALERT ----------
        if (!$isPresent) {

            $duplicateStmt = $pdo->prepare("
                SELECT id FROM parent_notifications
                WHERE student_id = ?
                  AND type = 'Absence'
                  AND DATE(created_at) = ?
                LIMIT 1
            ");
            $duplicateStmt->execute([$studentDbId, $today]);

            if (!$duplicateStmt->fetch()) {

                $message = "Dear Parent, your ward $studentName is absent today ($today). Please take note of the attendance.";

                $insert = $pdo->prepare("
                    INSERT INTO parent_notifications
                    (student_id, type, message, channel, status)
                    VALUES (?, 'Absence', ?, 'Email', 'Pending')
                ");
                $insert->execute([$studentDbId, $message]);

                $notificationId = (int)$pdo->lastInsertId();
                $created++;

                if ($parentEmail !== "") {
                    $result = sendParentEmail(
                        $parentEmail,
                        $studentName,
                        $today,
                        "Absence"
                    );

                    $newStatus = $result["success"] ? "Sent" : "Failed";

                    $update = $pdo->prepare("
                        UPDATE parent_notifications
                        SET status = ?, channel = 'Email'
                        WHERE id = ?
                    ");
                    $update->execute([$newStatus, $notificationId]);

                    if ($result["success"]) {
                        $sent++;
                    } else {
                        $failed++;
                    }
                } else {
                    $update = $pdo->prepare("
                        UPDATE parent_notifications
                        SET status = 'Failed', channel = 'Email'
                        WHERE id = ?
                    ");
                    $update->execute([$notificationId]);
                    $failed++;
                }
            }
        }

        // ---------- LOW ATTENDANCE ALERT ----------
        $attendanceStmt = $pdo->prepare("
            SELECT
                COUNT(*) AS total_classes,
                SUM(status = 'Present') AS present_classes
            FROM attendance
            WHERE student_id = ?
        ");
        $attendanceStmt->execute([$studentDbId]);
        $attendance = $attendanceStmt->fetch();

        $totalClasses = (int)($attendance["total_classes"] ?? 0);
        $presentClasses = (int)($attendance["present_classes"] ?? 0);

        if ($totalClasses > 0) {
            $percentage = ($presentClasses / $totalClasses) * 100;

            if ($percentage < 75) {

                $duplicateLowStmt = $pdo->prepare("
                    SELECT id FROM parent_notifications
                    WHERE student_id = ?
                      AND type = 'Low Attendance'
                      AND DATE(created_at) = ?
                    LIMIT 1
                ");
                $duplicateLowStmt->execute([$studentDbId, $today]);

                if (!$duplicateLowStmt->fetch()) {

                    $percentageText = number_format($percentage, 1);

                    $message = "Dear Parent, your ward $studentName has attendance of $percentageText%, which is below 75%. Please take necessary action.";

                    $insertLow = $pdo->prepare("
                        INSERT INTO parent_notifications
                        (student_id, type, message, channel, status)
                        VALUES (?, 'Low Attendance', ?, 'Email', 'Pending')
                    ");
                    $insertLow->execute([$studentDbId, $message]);

                    $notificationId = (int)$pdo->lastInsertId();
                    $created++;

                    if ($parentEmail !== "") {
                        $result = sendParentEmail(
                            $parentEmail,
                            $studentName,
                            $today,
                            "Low Attendance"
                        );

                        $newStatus = $result["success"] ? "Sent" : "Failed";

                        $update = $pdo->prepare("
                            UPDATE parent_notifications
                            SET status = ?, channel = 'Email'
                            WHERE id = ?
                        ");
                        $update->execute([$newStatus, $notificationId]);

                        if ($result["success"]) {
                            $sent++;
                        } else {
                            $failed++;
                        }
                    } else {
                        $update = $pdo->prepare("
                            UPDATE parent_notifications
                            SET status = 'Failed', channel = 'Email'
                            WHERE id = ?
                        ");
                        $update->execute([$notificationId]);
                        $failed++;
                    }
                }
            }
        }
    }

    if ($created === 0) {
        $msg = "No new parent alerts were required today.";
    } else {
        $msg = "Alerts created: $created | Emails sent: $sent | Failed: $failed";
        if ($failed > 0) {
            $msgType = "warning";
        }
    }
}

$rows = $pdo->query("
    SELECT
        n.*,
        s.student_id,
        s.name,
        s.parent_email
    FROM parent_notifications n
    JOIN students s ON s.id = n.student_id
    ORDER BY n.id DESC
    LIMIT 100
")->fetchAll();

require "includes/header.php";
?>

<?php if ($msg): ?>
<div class="alert <?= $msgType === "warning" ? "warning" : "success" ?>">
    <?= h($msg) ?>
</div>
<?php endif; ?>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Parent Alerts</h2>
            <p>Automatic email alerts for absence and low attendance.</p>
        </div>

        <form method="post">
            <input type="hidden" name="action" value="generate_alerts">
            <button class="btn primary" type="submit">
                Generate &amp; Send Today's Alerts
            </button>
        </form>
    </div>

    <div class="info-box">
        The system checks today's attendance and sends an email to the parent's
        registered email address. Each alert is generated only once per day.
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Parent Email</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <b><?= h($r["name"] ?? "") ?></b>
                        <small class="sub"><?= h($r["student_id"] ?? "") ?></small>
                    </td>
                    <td><?= h($r["parent_email"] ?? "") ?></td>
                    <td><?= h($r["type"] ?? "") ?></td>
                    <td class="message-cell"><?= h($r["message"] ?? "") ?></td>
                    <td><?= h($r["channel"] ?? "") ?></td>
                    <td>
                        <?php
                        $status = strtolower($r["status"] ?? "");
                        $badgeClass = $status === "sent"
                            ? "present"
                            : ($status === "failed" ? "absent" : "warning");
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= h($r["status"] ?? "") ?>
                        </span>
                    </td>
                    <td><?= h($r["created_at"] ?? "") ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$rows): ?>
                <tr>
                    <td colspan="7" class="empty">No parent alerts yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require "includes/footer.php"; ?>

<?php
require_once "includes/auth.php";
require_role(["Admin"]);
require_once "config/database.php";

$pageTitle = "Faculty";
$msg = "";
$msgType = "success";

/* ADD FACULTY */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $department = trim($_POST["department"] ?? "");

    if ($name === "") {
        $msg = "Please enter faculty name.";
        $msgType = "error";
    } else {
        try {
            $st = $pdo->prepare(
                "INSERT INTO faculty (name, email, department)
                 VALUES (?, ?, ?)"
            );

            $st->execute([
                $name,
                $email,
                $department
            ]);

            $msg = "Faculty member added successfully.";
            $msgType = "success";

        } catch (Exception $e) {
            $msg = "Could not add faculty: " . $e->getMessage();
            $msgType = "error";
        }
    }
}


/* DELETE FACULTY */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete") {

    $facultyId = (int)($_POST["faculty_id"] ?? 0);

    if ($facultyId > 0) {
        try {

            $st = $pdo->prepare(
                "DELETE FROM faculty WHERE id = ?"
            );

            $st->execute([$facultyId]);

            $msg = "Faculty member deleted successfully.";
            $msgType = "success";

        } catch (Exception $e) {

            $msg = "Could not delete faculty: " . $e->getMessage();
            $msgType = "error";
        }
    }
}


/* GET FACULTY */
$rows = $pdo->query(
    "SELECT id, name, email, department
     FROM faculty
     ORDER BY name"
)->fetchAll();


require "includes/header.php";
?>


<?php if ($msg): ?>

<div class="alert <?= h($msgType) ?>">
    <?= h($msg) ?>
</div>

<?php endif; ?>


<section class="panel">

    <div class="panel-head">

        <div>
            <h2>Faculty</h2>
            <p>Faculty records for the attendance system.</p>
        </div>

        <button
            class="btn primary"
            type="button"
            onclick="toggleModal('facultyModal')">
            + Add Faculty
        </button>

    </div>


    <div class="table-wrap">

        <table>

            <thead>

                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>

            </thead>


            <tbody>

            <?php foreach ($rows as $r): ?>

                <tr>

                    <td>
                        <b><?= h($r["name"] ?? "") ?></b>
                    </td>

                    <td>
                        <?= h($r["email"] ?? "") ?>
                    </td>

                    <td>
                        <?= h($r["department"] ?? "") ?>
                    </td>

                    <td>

                        <form
                            method="post"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this faculty member?');">

                            <input
                                type="hidden"
                                name="action"
                                value="delete">

                            <input
                                type="hidden"
                                name="faculty_id"
                                value="<?= (int)($r["id"] ?? 0) ?>">

                            <button
                                type="submit"
                                class="btn"
                                style="background:#dc2626;color:white;">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>


            <?php if (!$rows): ?>

                <tr>

                    <td colspan="4" class="empty">
                        No faculty records.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>



<!-- ADD FACULTY MODAL -->

<div class="modal" id="facultyModal">

    <div class="modal-card small">

        <div class="panel-head">

            <h2>Add Faculty</h2>

            <button
                type="button"
                class="close"
                onclick="toggleModal('facultyModal')">
                ×
            </button>

        </div>


        <form method="post">

            <input
                type="hidden"
                name="action"
                value="add">


            <label>Name</label>

            <input
                type="text"
                name="name"
                required
                autocomplete="off">


            <label>Email</label>

            <input
                type="email"
                name="email"
                autocomplete="off">


            <label>Department</label>

            <input
                type="text"
                name="department"
                autocomplete="off">


            <button
                type="submit"
                class="btn primary full">
                Save Faculty
            </button>

        </form>

    </div>

</div>


<?php require "includes/footer.php"; ?>
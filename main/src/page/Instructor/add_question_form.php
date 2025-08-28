<?php
    session_start();
    require_once('../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล
    if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
        $_SESSION['error'] = "You must be an Instructor to view course details.";
        header("Location: login.php");
        exit;
    }
    // ตรวจสอบว่ามีการส่ง quiz_id มาหรือไม่
    if (!isset($_GET['quiz_id']) || empty($_GET['quiz_id'])) {
        $_SESSION['error'] = "Invalid course ID.";
        header("Location: quizzes.php");
        exit;
    }
    // รับ quiz_id จาก URL
    $quiz_id = intval($_GET['quiz_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $_SESSION['success']; ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <h2>Add New Question</h2>
    <form action="../../system/Add_quiz_questions.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
        <div class="mb-3">
            <label for="question_text" class="form-label">Question</label>
            <textarea class="form-control" id="question_text" name="question_text" required></textarea>
        </div>
        <div class="mb-3">
            <label for="option_a" class="form-label">Option A</label>
            <input type="text" class="form-control" id="option_a" name="option_a" required>
        </div>
        <div class="mb-3">
            <label for="option_b" class="form-label">Option B</label>
            <input type="text" class="form-control" id="option_b" name="option_b" required>
        </div>
        <div class="mb-3">
            <label for="option_c" class="form-label">Option C</label>
            <input type="text" class="form-control" id="option_c" name="option_c" required>
        </div>
        <div class="mb-3">
            <label for="option_d" class="form-label">Option D</label>
            <input type="text" class="form-control" id="option_d" name="option_d" required>
        </div>
        <div class="mb-3">
            <label for="correct_answer" class="form-label">Correct Answer</label>
            <select class="form-select" id="correct_answer" name="correct_answer" required>
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

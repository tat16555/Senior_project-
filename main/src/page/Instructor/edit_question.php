<?php
session_start();
require_once('../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่า มีการส่ง question_id มาหรือไม่
if (!isset($_GET['question_id']) || empty($_GET['question_id'])) {
    $_SESSION['error'] = "Invalid question ID.";
    header("Location: course_list.php"); // หรือหน้าอื่นๆ ที่ต้องการ
    exit;
}

// รับ question_id จาก URL
$question_id = intval($_GET['question_id']);

// เชื่อมต่อฐานข้อมูล
$dbController = new DBController();
$conn = $dbController->getConn();

// ดึงข้อมูลคำถามจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE question_id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ดึงข้อมูลคำถาม
    $question = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Question not found.";
    header("Location: course_list.php"); // หรือหน้าอื่นๆ ที่ต้องการ
    exit;
}
?>

<!-- แสดงข้อมูลคำถามในฟอร์มเพื่อแก้ไข -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Question</h2>
        <!-- แสดงข้อมูลคำถามที่ดึงมาเพื่อให้แก้ไข -->
        <form action="../../system/Edit/edit_question.php" method="post">
            <div class="mb-3">
                <label for="question_text" class="form-label">Question Text</label>
                <input type="text" class="form-control" id="question_text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_a" class="form-label">Option A</label>
                <input type="text" class="form-control" id="option_a" name="option_a" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_b" class="form-label">Option B</label>
                <input type="text" class="form-control" id="option_b" name="option_b" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_c" class="form-label">Option C</label>
                <input type="text" class="form-control" id="option_c" name="option_c" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_d" class="form-label">Option D</label>
                <input type="text" class="form-control" id="option_d" name="option_d" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Correct Answer</label>
                <input type="text" class="form-control" id="correct_answer" name="correct_answer" value="<?php echo htmlspecialchars($question['correct_answer']); ?>" required>
            </div>
            <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include("header.php");
// تأكد من أن المستخدم قد قام بتسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "app_store");

// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// إعداد الاستعلام لجلب التطبيقات الخاصة بالمستخدم
$stmt = $conn->prepare("SELECT name, description, file_path, image_path FROM apps WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    // التأكد من وجود نتائج
    if ($result && $result->num_rows > 0):
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيقاتي</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">تطبيقاتي</h2>
    <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top" alt="صورة التطبيق">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-primary" download>تحميل التطبيق</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
<?php
    else:
        echo "<div class='alert alert-warning'>لا توجد تطبيقات لعرضها.</div>";
    endif;
} else {
    echo "<div class='alert alert-danger'>حدث خطأ أثناء جلب البيانات.</div>";
}

// إغلاق الاستعلام والاتصال
$stmt->close();
$conn->close();
?>

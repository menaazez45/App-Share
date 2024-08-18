<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("connect.php");
session_start();

// التحقق من إرسال البيانات ومن تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $app_id = intval($_POST['app_id']);
    $user_id = intval($_SESSION['user_id']);
    $rating = intval($_POST['rating']);
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    // التحقق من صحة التقييم
    if ($rating < 1 || $rating > 5) {
        echo "<div class='alert alert-danger'>التقييم يجب أن يكون بين 1 و 5 نجوم.</div>";
        exit();
    }

    // التحقق من صحة app_id
    if ($app_id <= 0) {
        echo "<div class='alert alert-danger'>معرف التطبيق غير صحيح.</div>";
        exit();
    }

    // التحقق من وجود تقييم مسبق للمستخدم
    $stmt = $conn->prepare("SELECT * FROM app_ratings WHERE app_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $app_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // إذا كان هناك تقييم موجود، نقوم بتحديثه
        $stmt = $conn->prepare("UPDATE app_ratings SET rating = ?, comment = ? WHERE app_id = ? AND user_id = ?");
        $stmt->bind_param("isii", $rating, $comment, $app_id, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>تم تحديث التقييم بنجاح!</div>";
        } else {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء تحديث التقييم.</div>";
        }
    } else {
        // إذا لم يكن هناك تقييم، نقوم بإدراجه
        $stmt = $conn->prepare("INSERT INTO app_ratings (app_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $app_id, $user_id, $rating, $comment);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>تم تقديم التقييم بنجاح!</div>";
        } else {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء تقديم التقييم.</div>";
        }
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>يرجى تسجيل الدخول لتقديم التقييم.</div>";
}

$conn->close();
?>

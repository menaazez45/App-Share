<?php
include("header.php");

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "app_store");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    // تحديث البيانات في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('تم تحديث الحساب بنجاح!');</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء تحديث الحساب.');</script>";
    }

    $stmt->close();
}

// جلب البيانات الحالية للمستخدم
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الحساب</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">إدارة الحساب</h2>
    <div class="card p-4 shadow-lg">
        <form action="account.php" method="post">
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $user_data['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_data['email']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">تحديث الحساب</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "app_store");

    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userId, $hashedPassword);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $userId;
        echo "<script>alert('تم تسجيل الدخول بنجاح!');</script>";
        header("Location: index.php");
    } else {
        echo "<script>alert('اسم المستخدم أو كلمة المرور غير صحيحة.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: bounceIn 1s ease-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.5); }
            60% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="text-center">تسجيل الدخول</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">تسجيل الدخول</button>
    </form>
</div>

</body>
</html>

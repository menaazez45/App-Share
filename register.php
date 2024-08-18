<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $conn = new mysqli("localhost", "root", "", "app_store");

    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    // التحقق من وجود اسم المستخدم أو البريد الإلكتروني مسبقًا
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // اسم المستخدم أو البريد الإلكتروني موجود مسبقًا
        echo "<script>alert('اسم المستخدم أو البريد الإلكتروني موجود مسبقًا. يرجى اختيار اسم مستخدم أو بريد إلكتروني آخر.');</script>";
    } else {
        // إدراج المستخدم الجديد إذا لم يكن موجودًا مسبقًا
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, pr) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('تم إنشاء الحساب بنجاح!');</script>";
            header("Location: login.php");
            exit();
        } else {
            echo "<script>alert('حدث خطأ أثناء إنشاء الحساب.');</script>";
        }
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
    <title>إنشاء حساب</title>
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
        .register-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2 class="text-center">إنشاء حساب</h2>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">إنشاء حساب</button>
    </form>
</div>

</body>
</html>

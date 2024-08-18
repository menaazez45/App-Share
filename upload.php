<?php
$targetDir = "mmm/";
$imageDir = "mmm/";
$screenshotsDir = "mmm/";
$uploadOk = 1;
include("header.php");

// تأكد من أن الدلائل موجودة، وإذا لم تكن موجودة، قم بإنشائها
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0755, true);
}
if (!file_exists($screenshotsDir)) {
    mkdir($screenshotsDir, 0755, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appName = $_POST['name'];
    $appDescription = $_POST['description'];
    $targetFile = $targetDir . basename($_FILES["appFile"]["name"]);
    
    // تحقق من صحة الملف (السماح بملفات APK و AAB)
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if($fileType != "apk" && $fileType != "aab") {
        echo "<div class='alert alert-danger'>فقط ملفات APK و AAB مسموحة.</div>";
        $uploadOk = 0;
    }

    // رفع صورة التطبيق الخارجية
    $imageFile = $imageDir . basename($_FILES["appImage"]["name"]);
    $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
        echo "<div class='alert alert-danger'>فقط ملفات JPG, JPEG, PNG مسموحة لصورة التطبيق.</div>";
        $uploadOk = 0;
    }

    // رفع صور السكرين شوت (5 صور كحد أقصى)
    $screenshotPaths = [];
    if (count($_FILES["screenshots"]["name"]) > 5) {
        echo "<div class='alert alert-danger'>يمكنك رفع 5 صور كحد أقصى للسكرين شوت.</div>";
        $uploadOk = 0;
    } else {
        foreach ($_FILES["screenshots"]["name"] as $index => $name) {
            $screenshotFile = $screenshotsDir . basename($name);
            $screenshotFileType = strtolower(pathinfo($screenshotFile, PATHINFO_EXTENSION));
            if (!in_array($screenshotFileType, ["jpg", "jpeg", "png"])) {
                echo "<div class='alert alert-danger'>فقط ملفات JPG, JPEG, PNG مسموحة لصور السكرين شوت.</div>";
                $uploadOk = 0;
                break;
            }
            if (!move_uploaded_file($_FILES["screenshots"]["tmp_name"][$index], $screenshotFile)) {
                echo "<div class='alert alert-danger'>حدث خطأ أثناء رفع صور السكرين شوت.</div>";
                $uploadOk = 0;
                break;
            }
            $screenshotPaths[] = $screenshotFile;
        }
    }
    
    // إذا كان التحقق سليمًا، قم برفع الملف
    if ($uploadOk && move_uploaded_file($_FILES["appFile"]["tmp_name"], $targetFile) 
                  && move_uploaded_file($_FILES["appImage"]["tmp_name"], $imageFile)) {
        
        // حفظ معلومات التطبيق في قاعدة البيانات
        if ($uploadOk) {
            $conn = new mysqli("localhost", "root", "", "app_store");

            if ($conn->connect_error) {
                die("فشل الاتصال: " . $conn->connect_error);
            }

            $user_id = $_SESSION['user_id']; // استخدم معرف المستخدم من الجلسة
            $screenshotsJSON = json_encode($screenshotPaths); // تحويل مسارات السكرين شوت إلى JSON
            $stmt = $conn->prepare("INSERT INTO apps (name, description, file_path, image_path, screenshots, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $appName, $appDescription, $targetFile, $imageFile, $screenshotsJSON, $user_id);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>تم رفع التطبيق بنجاح!</div>";
            } else {
                echo "<div class='alert alert-danger'>حدث خطأ أثناء حفظ التطبيق.</div>";
            }

            $stmt->close();
            $conn->close();
        }
    } else {
        echo "<div class='alert alert-danger'>حدث خطأ أثناء رفع الملف.</div>";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">رفع تطبيق جديد</h2>
    <div class="card p-4 shadow-lg">
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">اسم التطبيق</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">وصف التطبيق</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="appFile">اختر ملف APK أو AAB</label>
                <input type="file" class="form-control-file" id="appFile" name="appFile" required>
            </div>
            <div class="form-group">
                <label for="appImage">اختر صورة التطبيق الخارجية</label>
                <input type="file" class="form-control-file" id="appImage" name="appImage" required>
            </div>
            <div class="form-group">
                <label for="screenshots">اختر حتى 5 صور سكرين شوت من داخل التطبيق</label>
                <input type="file" class="form-control-file" id="screenshots" name="screenshots[]" multiple required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">رفع التطبيق</button>
        </form>
    </div>
</div>

<?php
include("connect.php");
include("header.php");

// التحقق من وجود معرف التطبيق في URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>معرف التطبيق غير صحيح.</div>";
    exit();
}

$app_id = intval($_GET['id']);

// جلب تفاصيل التطبيق من قاعدة البيانات
$sql = "SELECT * FROM apps WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>لم يتم العثور على التطبيق.</div>";
    exit();
}

$app = $result->fetch_assoc();

// جلب تقييمات التطبيق
$ratings_sql = "SELECT AVG(rating) AS average_rating FROM app_ratings WHERE app_id = ?";
$ratings_stmt = $conn->prepare($ratings_sql);
$ratings_stmt->bind_param("i", $app_id);
$ratings_stmt->execute();
$ratings_result = $ratings_stmt->get_result();
$average_rating = $ratings_result->fetch_assoc()['average_rating'];
?>

<div class="container mt-5">
    <h2 class="text-center mb-4"><?= htmlspecialchars($app['name']) ?></h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="<?= htmlspecialchars($app['image_path']) ?>" class="img-fluid" alt="صورة التطبيق">
        </div>
        <div class="col-md-6 mb-4">
            <h4>وصف التطبيق:</h4>
            <p><?= htmlspecialchars($app['description']) ?></p>
            <a id="downloadButton" class="btn btn-primary" href="#">تحميل التطبيق</a>
            <p class="mt-2">يرجى التأكد من تمكين التثبيت من مصادر غير معروفة في إعدادات هاتفك.</p>
        </div>
    </div>
    <div class="row">
        <?php
        // عرض صور السكرين شوت إذا كانت موجودة
        $screenshots = json_decode($app['screenshots'], true);
        if (is_array($screenshots) && count($screenshots) > 0): ?>
            <div class="col-12">
                <h4>صور السكرين شوت:</h4>
                <div class="row">
                    <?php foreach ($screenshots as $screenshot): ?>
                        <div class="col-md-4 mb-4">
                            <img src="<?= htmlspecialchars($screenshot) ?>" class="img-fluid" alt="سكرين شوت">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- عرض التقييمات -->
    <div class="mt-4">
        <h4>التقييمات:</h4>
        <p>متوسط التقييم: <?= round($average_rating, 1) ?> نجوم</p>
    </div>

    <!-- نموذج التقييم والتعليق -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mt-4">
            <h4>تقييم هذا التطبيق:</h4>
            <form action="submit_rating.php" method="POST">
                <input type="hidden" name="app_id" value="<?= htmlspecialchars($app_id) ?>">
                <div class="form-group">
                    <label for="rating">التقييم:</label>
                    <select class="form-control" id="rating" name="rating" required>
                        <option value="1">1 نجمة</option>
                        <option value="2">2 نجوم</option>
                        <option value="3">3 نجوم</option>
                        <option value="4">4 نجوم</option>
                        <option value="5">5 نجوم</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">تعليق (اختياري):</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">إرسال التقييم</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">يرجى تسجيل الدخول لتقييم هذا التطبيق.</div>
    <?php endif; ?>

    <!-- زر المشاركة -->
    <div class="mt-4">
        <h4>مشاركة التطبيق:</h4>
        <a href="https://twitter.com/share?url=<?= urlencode('http://yourwebsite.com/app_details.php?id=' . $app_id) ?>" target="_blank" class="btn btn-info">مشاركة على تويتر</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://yourwebsite.com/app_details.php?id=' . $app_id) ?>" target="_blank" class="btn btn-primary">مشاركة على فيسبوك</a>
    </div>
</div>

<?php
$stmt->close();
$ratings_stmt->close();
$conn->close();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var downloadButton = document.getElementById('downloadButton');
    if (downloadButton) {
        downloadButton.addEventListener('click', function() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            console.log('User Agent:', userAgent);

            // تحقق من نوع الجهاز
            if (/android/i.test(userAgent)) {
                // إذا كان الجهاز Android
                var androidPath = "<?= htmlspecialchars($app['mmm/'], ENT_QUOTES, 'UTF-8') ?>";
                console.log('Android Path:', androidPath);
                window.location.href = androidPath; // رابط APK
            } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                // إذا كان الجهاز iOS
                var iosPath = "<?= htmlspecialchars($app['mmm/'], ENT_QUOTES, 'UTF-8') ?>";
                console.log('iOS Path:', iosPath);
                window.location.href = iosPath; // رابط Bundle
            } else {
                // إذا كان الجهاز كمبيوتر أو أي جهاز آخر
                var windowsPath = "<?= htmlspecialchars($app['mmm/'], ENT_QUOTES, 'UTF-8') ?>";
                console.log('Windows Path:', windowsPath);
                window.location.href = windowsPath; // رابط APK
            }
        });
    } else {
        console.error('زر التحميل غير موجود في الصفحة.');
    }
});
</script>

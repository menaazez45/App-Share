<?php
include("connect.php");
include("header.php");

// التحقق من إدخال البحث
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// جلب التطبيقات من قاعدة البيانات مع استخدام البحث إذا كان موجودًا
$sql = "SELECT * FROM apps WHERE name LIKE ? OR description LIKE ? ORDER BY upload_date DESC";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $searchQuery . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">التطبيقات المتاحة</h2>

    <!-- نموذج البحث -->
    <form class="form-inline mb-4 justify-content-center" method="GET" action="">
        <input class="form-control mr-2" type="text" name="search" placeholder="ابحث عن تطبيق..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="btn btn-primary" type="submit">بحث</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg h-100">
                        <img src="<?= htmlspecialchars($row["image_path"]) ?>" class="card-img-top" alt="صورة التطبيق" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row["name"]) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($row["description"]) ?></p>
                            <a href="app_details.php?id=<?= $row['id'] ?>" class="btn btn-primary mt-auto">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            لا توجد تطبيقات مطابقة لبحثك.
        </div>
    <?php endif; ?>

</div>

<?php
$stmt->close();
$conn->close();
?>

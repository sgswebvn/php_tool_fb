<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Gói hiện tại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/fanpages">Fanpages</a></li>
                    <li class="nav-item"><a class="nav-link" href="/posts">Bài viết</a></li>
                    <li class="nav-item"><a class="nav-link" href="/messages">Tin nhắn</a></li>
                    <li class="nav-item"><a class="nav-link" href="/settings">Cài đặt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/transactions">Lịch sử Giao dịch</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/subscription">Gói hiện tại</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gói hiện tại</h1>
            </div>
            <?php if ($flash = get_flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php elseif ($flash = get_flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gói hiện tại: <?= $plan['name'] ?? 'Free' ?></h5>
                    <p class="card-text">Số Fanpage tối đa: <?= $plan['max_pages'] ?? 1 ?></p>
                    <p class="card-text">Thời gian hết hạn: <?= $subscription['expires_at'] ? date('d/m/Y', strtotime($subscription['expires_at'])) : 'Không giới hạn' ?></p>
                    <a href="/upgrade" class="btn btn-primary">Nâng cấp Gói</a>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
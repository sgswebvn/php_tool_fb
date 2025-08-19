<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/settings">Cài đặt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/transactions">Lịch sử Giao dịch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Cài đặt</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin Người dùng</h5>
                    <p class="card-text">Tên: <?= htmlspecialchars($user['name']) ?></p>
                    <p class="card-text">Email: <?= htmlspecialchars($user['email']) ?></p>
                    <a href="#" class="btn btn-primary">Cập nhật Thông tin</a>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
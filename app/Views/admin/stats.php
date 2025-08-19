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
                        <a class="nav-link active" href="/admin/stats">Thống kê</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/users">Quản lý Người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/plans">Quản lý Gói</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Thống kê</h1>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Người dùng</h5>
                            <p class="card-text"><?= $totalUsers ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Thanh toán</h5>
                            <p class="card-text"><?= $totalPayments ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng Doanh thu</h5>
                            <p class="card-text"><?= number_format($totalRevenue) ?> VND</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
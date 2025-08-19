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
                        <a class="nav-link" href="/admin/stats">Thống kê</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/users">Quản lý Người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/plans">Quản lý Gói</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý Gói</h1>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Gói</th>
                            <th>Max Pages</th>
                            <th>Giá (VND)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                            <tr>
                                <td><?= $plan['id'] ?></td>
                                <td><?= $plan['name'] ?></td>
                                <td><?= $plan['max_pages'] ?></td>
                                <td><?= number_format($plan['price']) ?></td>
                                <td>
                                    <form method="POST" action="/admin/plans/update">
                                        <input type="hidden" name="id" value="<?= $plan['id'] ?>">
                                        <input type="text" name="name" value="<?= $plan['name'] ?>" class="form-control form-control-sm" style="display:inline; width:100px;">
                                        <input type="number" name="max_pages" value="<?= $plan['max_pages'] ?>" class="form-control form-control-sm" style="display:inline; width:80px;">
                                        <input type="number" name="price" value="<?= $plan['price'] ?>" class="form-control form-control-sm" style="display:inline; width:100px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>
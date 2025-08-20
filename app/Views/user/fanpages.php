<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Quản lý Fanpage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/fanpages">Fanpages</a></li>
                    <li class="nav-item"><a class="nav-link" href="/posts">Bài viết</a></li>
                    <li class="nav-item"><a class="nav-link" href="/messages">Tin nhắn</a></li>
                    <li class="nav-item"><a class="nav-link" href="/settings">Cài đặt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/transactions">Lịch sử Giao dịch</a></li>
                    <li class="nav-item"><a class="nav-link" href="/subscription">Gói hiện tại</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý Fanpage</h1>
            </div>
            <?php if ($flash = get_flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php elseif ($flash = get_flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
            <a href="/facebook/connect" class="btn btn-primary mb-3">Kết nối với Facebook</a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>FB ID</th>
                            <th>Category</th>
                            <th>Kết nối lúc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td><?= htmlspecialchars($page['name']) ?></td>
                                <td><?= $page['fb_page_id'] ?></td>
                                <td><?= htmlspecialchars($page['category']) ?></td>
                                <td><?= $page['connected_at'] ?></td>
                                <td>
                                    <form method="POST" action="/fanpage/delete/<?= $page['id'] ?>" onsubmit="return confirm('Bạn có chắc?')">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
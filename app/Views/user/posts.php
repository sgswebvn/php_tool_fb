<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Quản lý Bài viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/fanpages">Fanpages</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/posts">Bài viết</a></li>
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
                <h1 class="h2">Quản lý Bài viết</h1>
            </div>
            <?php if ($flash = get_flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php elseif ($flash = get_flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
            <form method="POST" action="/posts/create">
                <div class="mb-3">
                    <label for="page_id" class="form-label">Chọn Fanpage</label>
                    <select class="form-select" name="page_id" required>
                        <?php foreach ($pages as $page): ?>
                            <option value="<?= $page['id'] ?>"><?= htmlspecialchars($page['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Nội dung</label>
                    <textarea class="form-control" name="message" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="scheduled_time" class="form-label">Lịch đăng</label>
                    <input type="datetime-local" class="form-control" name="scheduled_time">
                </div>
                <button type="submit" class="btn btn-primary">Đăng bài</button>
            </form>
            <div class="table-responsive mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['message']) ?></td>
                                <td><?= $post['status'] ?></td>
                                <td><?= $post['created_time'] ?></td>
                                <td>
                                    <form method="POST" action="/posts/delete/<?= $post['id'] ?>" onsubmit="return confirm('Bạn có chắc?')">
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
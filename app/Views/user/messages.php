<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Quản lý Tin nhắn</title>
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
                        <a class="nav-link" href="/fanpages">Fanpages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/posts">Bài viết</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/messages">Tin nhắn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/settings">Cài đặt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/transactions">Lịch sử Giao dịch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/subscription">Gói hiện tại</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý Tin nhắn</h1>
            </div>
            <?php if ($flash = get_flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php elseif ($flash = get_flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Conversation ID</th>
                            <th>Trạng thái</th>
                            <th>Tin nhắn cuối</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conversations as $conv): ?>
                            <tr>
                                <td><?= $conv['fb_conversation_id'] ?></td>
                                <td><?= $conv['status'] ?></td>
                                <td><?= $conv['last_message_time'] ?></td>
                                <td>
                                    <form method="POST" action="/messages/send">
                                        <input type="hidden" name="conv_id" value="<?= $conv['id'] ?>">
                                        <input type="text" name="message" class="form-control form-control-sm d-inline" placeholder="Nhập tin nhắn" required>
                                        <button type="submit" class="btn btn-primary btn-sm">Gửi</button>
                                    </form>
                                    <form method="POST" action="/conversations/delete/<?= $conv['id'] ?>" onsubmit="return confirm('Bạn có chắc?')">
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
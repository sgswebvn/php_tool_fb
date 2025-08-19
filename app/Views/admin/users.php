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
                        <a class="nav-link active" href="/admin/users">Quản lý Người dùng</a>
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
                <h1 class="h2">Quản lý Người dùng</h1>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Gói</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['role'] ?></td>
                                <td><?= $user['status'] ?></td>
                                <td><?= $user['subscription']['plan_id'] ?? 'Free' ?></td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" action="/admin/users/block/<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Block</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/admin/users/unblock/<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Unblock</button>
                                        </form>
                                    <?php endif; ?>
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
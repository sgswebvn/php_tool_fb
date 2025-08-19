<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/settings">Cài đặt</a>
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

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="pageSelect">Chọn Fanpage: </label>
                        <select class="form-select" id="pageSelect" onchange="window.location.href='/dashboard?page_id='+this.value">
                            <option value="">Tất cả Fanpage</option>
                            <?php

                            use App\Models\Comment;

                            foreach ($pages as $page): ?>
                                <option value="<?= $page['id'] ?>" <?= $page['id'] == $selectedPageId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($page['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="/facebook/connect" class="btn btn-primary mt-2">Kết nối với Facebook</a>
                    </div>
                </div>
            </div>

            <h2>Fanpage</h2>
            <div class="list-group">
                <?php if (empty($pages)): ?>
                    <p class="text-muted">Chưa có Fanpage nào. Vui lòng kết nối với Facebook để bắt đầu.</p>
                <?php else: ?>
                    <?php foreach ($pages as $page): ?>
                        <a href="#" class="list-group-item list-group-item-action">
                            <?= htmlspecialchars($page['name']) ?> (<?= $page['fb_page_id'] ?>)
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h2 class="mt-4">Bài viết</h2>
            <div class="list-group">
                <?php foreach ($posts as $post): ?>
                    <a href="#" class="list-group-item list-group-item-action">
                        <?= htmlspecialchars($post['message']) ?> (<?= $post['status'] ?>)
                        <div class="ms-2">
                            <?php $comments = Comment::getByPostId($post['id']); ?>
                            <?php foreach ($comments as $comment): ?>
                                <small><?= htmlspecialchars($comment['message']) ?> (by <?= $comment['from_id'] ?>)</small><br>
                            <?php endforeach; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <h2 class="mt-4">Tin nhắn</h2>
            <div class="list-group">
                <?php foreach ($conversations as $conv): ?>
                    <a href="#" class="list-group-item list-group-item-action">
                        <?= $conv['fb_conversation_id'] ?> (<?= $conv['status'] ?>)
                    </a>
                <?php endforeach; ?>
            </div>

            <h2 class="mt-4">Thông tin Gói</h2>
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
</body>

</html>
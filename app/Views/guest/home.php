<!DOCTYPE html>
<html lang="vi">

<head>
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }

        /* Phong cách FB */
        .hero {
            background-color: #4267B2;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
        }

        .services {
            padding: 50px 0;
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">Fanpage Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Dịch vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white" href="/register">Đăng ký</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1>Quản Lý Fanpage Facebook Dễ Dàng</h1>
            <p>Hệ thống hiện đại giúp bạn theo dõi tin nhắn, bài viết, bình luận và hơn thế nữa.</p>
            <a href="/register" class="btn btn-light btn-lg">Bắt đầu miễn phí</a>
        </div>
    </section>

    <section class="services bg-white">
        <div class="container">
            <h2 class="text-center mb-4">Dịch vụ của chúng tôi</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Quản lý Fanpage</h5>
                            <p class="card-text">Kết nối và theo dõi nhiều fanpage cùng lúc.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Theo dõi Tin nhắn</h5>
                            <p class="card-text">Quản lý inbox realtime từ Facebook.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Phân tích Bình luận</h5>
                            <p class="card-text">Lọc và xử lý bình luận dưới bài viết.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2025 Fanpage Manager. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
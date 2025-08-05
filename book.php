<?php
include 'helpers.php';
auto_return_books();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Buku tidak ditemukan.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    echo "Buku tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user()) {
    $user_id = current_user()['id'];

    if (isset($_POST['favorite_add'])) {
        $check = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND book_id = ?");
        $check->bind_param("ii", $user_id, $book['id']);
        $check->execute();
        $exist = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$exist) {
            $stmt = $conn->prepare("INSERT INTO favorites (user_id, book_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $book['id']);
            $stmt->execute();
            $stmt->close();
            notify("✅ Buku ditambahkan ke favorit.");
        } else {
            notify("ℹ️ Buku sudah ada di favorit.");
        }
    }

    if (isset($_POST['favorite_remove'])) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND book_id = ?");
        $stmt->bind_param("ii", $user_id, $book['id']);
        $stmt->execute();
        $stmt->close();
        notify("❌ Buku dihapus dari favorit.");
    }
}

$isFavorite = false;
if (current_user()) {
    $stmt = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", current_user()['id'], $book['id']);
    $stmt->execute();
    $stmt->store_result();
    $isFavorite = $stmt->num_rows > 0;
    $stmt->close();
}

$search = strtolower($_GET['search'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .header {
            background: url('css/images/image.jpg') center/cover no-repeat;
            position: relative;
            color: white;
            margin-bottom: 30px;
            overflow: hidden;
            text-align: center;
            padding-bottom: 50px;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .header h1, .header .top-nav {
            position: relative;
            z-index: 1;
        }

        .header .top-nav {
            padding: 20px;
        }

        .header .top-nav a {
            margin: 0 12px;
            text-decoration: none;
            font-weight: bold;
            color: #ccc;
            transition: color 0.3s;
        }

        .header .top-nav a:hover {
            color: #fff;
        }

        h1 {
            text-align: center;
            margin: 40px 80px 80px 80px;
        }

        .search {
            max-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 5;
            bottom: 80px;
        }

        .search form {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 40px;
        }

        .search form input {
            width: 80%;
            height: 60px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            margin-top: -100px;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .book-detail {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; 
        }

        .book-detail img {
            width: 200px;
            border-radius: 5px;
        }

        .book-info {
            flex: 1;
        }

        .btn {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn.secondary { background-color: #f39c12; }
        .btn.danger { background-color: #e74c3c; }
        .btn:hover { opacity: 0.9; }

        footer {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            background-color: #eee;
            font-weight: bold;
        }

        a { text-decoration: none; color: #6f6f6f; font-weight: bold; }
        a:hover { color: #3498db; }

        #hamburger-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            font-size: 28px;
            cursor: pointer;
            color: white;
            background: rgba(0,0,0,0.5);
            padding: 6px 12px;
            border-radius: 4px;
            display: none;
        }

        #close-btn {
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            color: white;
            text-align: right;
        }

        .sidebar-menu {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: #222;
            color: white;
            z-index: 1000;
            padding-top: 60px;
            transition: left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-menu a {
            color: #ccc;
            padding: 15px 20px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .sidebar-menu a:hover {
            background-color: #444;
            color: #fff;
        }

        .sidebar-menu form {
            margin: 15px;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 999;
            display: none;
        }

        @media (max-width: 900px) {
            .top-nav { display: none; }
            #hamburger-btn { display: block; }
            .book-detail { flex-direction: column; align-items: center; }

           .header h1{
                font-size: 20px;
                margin: 60px 20px 20px;
            }
        }
    </style>
</head>
<body>

<div id="hamburger-btn">☰</div>

<nav class="sidebar-menu" id="sidebarMenu">
    <div id="close-btn">✖</div>
    <a href="index.php">Beranda</a>
    <a href="informasi.php">Informasi</a>
    <a href="pustakawan.php">Pustakawan</a>
    <?php if (current_user()): ?>
        <a href="profile.php">Profil</a>
        <?php if (is_admin()): ?>
            <a href="admin/dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Daftar</a>
    <?php endif; ?>
    <br>
    <form class="search-form" method="get" action="index.php">
        <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
    </form>
</nav>

<div id="overlay"></div>

<header class="header">
    <div class="top-nav">
        <?php if (current_user()): ?>
            <a href="index.php">Beranda</a>
            <a href="profile.php">Profil</a>
            <?php if (is_admin()): ?>
                <a href="admin/dashboard.php">Dashboard Admin</a>
            <?php endif; ?>
            <a href="informasi.php">Informasi</a>
            <a href="pustakawan.php">Pustakawan</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Daftar</a>
        <?php endif; ?>
    </div> 
    <h1>Perpustakaan Desa Kertasari<br> Kec. Pangkalan</h1>
</header>

<div class="search">
    <form method="get" action="index.php">
        <input type="text" name="search" placeholder="Cari judul buku..." value="<?= htmlspecialchars($search) ?>">
    </form>
</div>

<div class="container">
    <a href="index.php">← Kembali ke Daftar Buku</a>

    <div class="book-detail">
        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Cover Buku">
        <div class="book-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Penulis:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Penerbit:</strong> <?= htmlspecialchars($book['publisher']) ?></p>
            <p><strong>Tahun Terbit:</strong> <?= htmlspecialchars($book['year']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($book['status']) ?></p>
            <p><strong>Stok Tersedia:</strong> <?= $book['stock'] ?> buah</p>
            <p><?= htmlspecialchars($book['description']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($book['category']) ?></p>

            <?php if (current_user()): ?>
                <form method="post">
                    <?php if ($isFavorite): ?>
                        <input type="hidden" name="favorite_remove" value="1">
                        <button type="submit" class="btn danger">🗑 Hapus dari Favorit</button>
                    <?php else: ?>
                        <input type="hidden" name="favorite_add" value="1">
                        <button type="submit" class="btn secondary">❤️ Tambah ke Favorit</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <?php if (current_user()): ?>
        <?php if ($book['stock'] > 0): ?>
            <form method="post" action="borrow.php">
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <label for="borrow_date">Tanggal Pinjam:</label>
                <input type="date" name="borrow_date" id="borrow_date" required>
                <label for="return_date">Tanggal Kembali:</label>
                <input type="date" name="return_date" id="return_date" required>
                <button type="submit" class="btn">📚 Ajukan Peminjaman</button>
            </form>
        <?php else: ?>
            <p><strong>📕 Buku sedang dipinjam atau stok habis.</strong></p>
        <?php endif; ?>
    <?php else: ?>
        <p><a href="login.php" class="btn danger">Login untuk meminjam atau menambahkan ke favorit</a></p>
    <?php endif; ?>
</div>

<footer style="text-align:center; padding:20px 0; background:#222; color:#eee;">
    © 2025 | Perpustakaan Desa Kertasari
</footer>


<script>
    const sidebar = document.getElementById('sidebarMenu');
    const hamburger = document.getElementById('hamburger-btn');
    const closeBtn = document.getElementById('close-btn');
    const overlay = document.getElementById('overlay');

    hamburger.addEventListener('click', () => {
        sidebar.style.left = '0';
        overlay.style.display = 'block';
    });

    closeBtn.addEventListener('click', () => {
        sidebar.style.left = '-250px';
        overlay.style.display = 'none';
    });

    overlay.addEventListener('click', () => {
        sidebar.style.left = '-250px';
        overlay.style.display = 'none';
    });
</script>

</body>
</html>

<?php
include 'helpers.php';
auto_return_books(); // tetap bisa jalan karena dia gunakan koneksi MySQL

$search = strtolower($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

// Hitung total
$totalResult = $conn->query("SELECT COUNT(*) as total FROM books WHERE LOWER(title) LIKE '%$search%'");
$total = $totalResult->fetch_assoc()['total'] ?? 0;

// Ambil data buku dari DB
$stmt = $conn->prepare("SELECT * FROM books WHERE LOWER(title) LIKE ? LIMIT ? OFFSET ?");
$like = "%$search%";
$stmt->bind_param('sii', $like, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

$search = strtolower($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

$conditions = "LOWER(title) LIKE ?";
$params = ["%$search%"];
$types = "s";

if ($category !== '') {
    $conditions .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Query jumlah total
$totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE $conditions");
$totalStmt->bind_param($types, ...$params);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$total = $totalResult->fetch_assoc()['total'] ?? 0;

// Query data buku
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";

$dataStmt = $conn->prepare("SELECT * FROM books WHERE $conditions LIMIT ? OFFSET ?");
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$result = $dataStmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perpustakaan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100%;
            margin: auto;
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

        main {
            margin-top: -100px;
            padding: 20px 80px 30px;
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

        .search form{
           display: flex;
           justify-content: center;
           width: 100%;
           margin-bottom: 40px;
        }

        .search form input{
            width: 80%;
            height: 60px;
        }

        .layout {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .sidebar {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            background-color: #fff;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .content {
            flex: 3;
            min-width: 0;
            padding: 0 15px;
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

        .book-card {
            display: flex;
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        }

        .book-card a {
            padding: 10px;
            color: #fff;
            text-decoration: none;
            background-color: #2196F3;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .book-card a:hover {
            background-color: #1269b0;
        }

        .book-card img {
            width: 100px;
            height: auto;
            margin-right: 15px;
            border-radius: 4px;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            padding: 6px 12px;
            margin: 2px;
            border: 1px solid #aaa;
            text-decoration: none;
            border-radius: 4px;
            color: #333;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
 
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

        .sidebar-menu form{
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

        .book-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .book-card {
            transition: box-shadow 0.3s ease, transform 0.3s ease, background-color 0.3s ease;
        }

        .book-card-link:hover .book-card {
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            transform: translateY(-6px);
            background-color: #edededff;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .layout {
                flex-direction: column;
                align-items: center;
            }
            main {
                padding: 20px 10px;
            }
            .search form input[type="text"]{
                max-width: 100%;
            }
            .header h1{
                font-size: 20px;
                margin: 60px 20px 20px;
            }
            .book-card {
                flex-direction: row;
            }
            .content {
                flex: 3;
                min-width: 100%;
                padding: 0 20px;
            }
            .sidebar {
                display: none;
            }
            #hamburger-btn {
                display: block;
            }
            .top-nav {
                display: none; 
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
    <form class="search-form" method="get">
        <input type="text" name="search" placeholder="Masukkan judul..." value="<?= htmlspecialchars($search) ?>">
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
            <form class="search-form" method="get">
                <input type="text" name="search" placeholder="Cari judul buku..." value="<?= htmlspecialchars($search) ?>">
            </form>
    </div>

<main>
    <div class="layout">
        <div class="sidebar">
            <h3>📂 Filter Kategori</h3>
            <form method="get" style="margin-bottom: 20px;">
                <select name="category" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php
                    $categories = $conn->query("SELECT DISTINCT category FROM books ORDER BY category ASC");
                    while ($cat = $categories->fetch_assoc()) {
                        $selected = ($category === $cat['category']) ? 'selected' : '';
                        echo "<option value=\"{$cat['category']}\" $selected>{$cat['category']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <div class="content">
            <?php if (empty($books)): ?>
                <p><i>Tidak ada buku ditemukan.</i></p>
            <?php else: ?>
                <?php foreach ($books as $b): ?>
                    <a href="book.php?id=<?= $b['id'] ?>" class="book-card-link">
                        <div class="book-card">
                            <img src="<?= htmlspecialchars($b['cover']) ?>" alt="Cover">
                            <div class="book-info">
                                <h3><?= htmlspecialchars($b['title']) ?></h3>
                                <p><strong>Penulis:</strong> <?= htmlspecialchars($b['author']) ?><br>
                                    <strong>Tahun:</strong> <?= htmlspecialchars($b['year']) ?><br>
                                    <strong>Kategori:</strong> <?= htmlspecialchars($b['category']) ?><br>
                                    <strong>Status:</strong> <?= htmlspecialchars($b['status']) ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="pagination">
                <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</main>

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

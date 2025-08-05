<?php
include 'helpers.php';
auto_return_books();

$user = current_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

// Ambil daftar buku favorit dari tabel relasi favorites
$favQuery = $conn->prepare("SELECT books.* FROM favorites 
    JOIN books ON books.id = favorites.book_id 
    WHERE favorites.user_id = ?");
$favQuery->bind_param('i', $user['id']);
$favQuery->execute();
$favBooks = $favQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Ambil riwayat peminjaman user
$histQuery = $conn->prepare("SELECT borrow_requests.*, books.title FROM borrow_requests 
    JOIN books ON books.id = borrow_requests.book_id 
    WHERE borrow_requests.user_id = ?");
$histQuery->bind_param('i', $user['id']);
$histQuery->execute();
$history = $histQuery->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .header {
            background: url('css/images/image.jpg') center/cover no-repeat;
            position: relative;
            color: white;
            padding-bottom: 50px;
            text-align: center;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
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

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: #ffffff;
            padding: 25px 30px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .book-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .book-card:hover {
            background-color: #efefef;
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .book-card img {
            width: 80px;
            height: auto;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            font-size: 14px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #f44336;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c62828;
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

        @media (max-width: 768px) {
            .book-list {
                grid-template-columns: 1fr 1fr;
            }

            #hamburger-btn {
                display: block;
            }

            .top-nav {
                display: none;
            }

            .header{
                margin-top: -20px;
            }

            .header h1 {
                font-size: 22px;
                padding-top: 50px;
            }
            .profile-card table td{
                font-size: 15px;
            }
            .profile-card table th{
                font-size: 15px;
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
    <a href="profile.php">Profil</a>
    <?php if (is_admin()): ?>
        <a href="admin/dashboard.php">Dashboard Admin</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<div id="overlay"></div>

<header class="header">
    <div class="top-nav">
        <a href="index.php">Beranda</a>
        <a href="profile.php">Profil</a>
        <?php if (is_admin()): ?>
            <a href="admin/dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
        <a href="informasi.php">Informasi</a>
        <a href="pustakawan.php">Pustakawan</a>
        <a href="logout.php">Logout</a>
    </div>
    <h1>Profil Pengguna</h1>
</header>

<div class="container">

    <div class="profile-card">
        <h2>👤 Informasi Pengguna</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Nomor HP:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
        <a href="logout.php" class="logout-btn">🔒 Logout</a>
    </div>

    <div class="profile-card">
        <h2>❤️ Buku Favorit</h2>
        <?php if (empty($favBooks)): ?>
            <p><i>Belum ada buku favorit.</i></p>
        <?php else: ?>
            <div class="book-list">
                <?php foreach ($favBooks as $book): ?>
                    <a href="book.php?id=<?= $book['id'] ?>" class="book-card">
                        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Cover">
                        <div><strong><?= htmlspecialchars($book['title']) ?></strong></div>
                        <small><?= htmlspecialchars($book['author']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="profile-card">
        <h2>📖 Riwayat Peminjaman</h2>
        <?php if (empty($history)): ?>
            <p><i>Belum ada riwayat peminjaman.</i></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['title']) ?></td>
                            <td><?= htmlspecialchars($h['borrow_date']) ?></td>
                            <td><?= htmlspecialchars($h['return_date']) ?></td>
                            <td><?= ucfirst($h['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

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

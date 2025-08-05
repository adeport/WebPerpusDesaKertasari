<?php
include 'helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Perpustakaan</title>
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
            padding-bottom: 50px;
            text-align: center;
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

        main {
            padding: 40px 80px;
        }

        h2 {
            color: #2c3e50;
            margin-top: 0;
        }

        p {
            font-size: 16px;
            line-height: 1.7;
            color: #333;
        }

        @media (max-width: 768px) {
            main {
                padding: 20px;
            }
            .header h1 {
                font-size: 22px;
                margin: 60px 20px 20px;
            }
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
            .header{
                margin-top: -60px;
            }
            .header h1{
                padding-top: 50px ;
                font-size: 20px; 
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
    <?php if (current_user()): ?>
        <a href="profile.php">Profil</a>
        <?php if (is_admin()): ?>
            <a href="admin/dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Daftar</a>
    <?php endif; ?>
    <a href="informasi.php">Informasi</a>
    <a href="pustakawan.php">Pustakawan</a>
</nav>

<div id="overlay"></div>

<header class="header">
    <div class="top-nav">
        <a href="index.php">Beranda</a>
        <?php if (current_user()): ?>
            <a href="profile.php">Profil</a>
            <?php if (is_admin()): ?>
                <a href="admin/dashboard.php">Dashboard Admin</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Daftar</a>
        <?php endif; ?>
        <a href="informasi.php">Informasi</a>
        <a href="pustakawan.php">Pustakawan</a>
    </div>
    <h1>Informasi Perpustakaan</h1>
</header>

<main>
    <h2>📚 Tentang Perpustakaan</h2>
    <p>
        Perpustakaan Desa Kertasari merupakan fasilitas literasi masyarakat yang berada di Kecamatan Pangkalan. 
        Dibentuk sebagai wadah untuk meningkatkan minat baca, memperluas wawasan, dan memberikan akses informasi 
        yang mudah serta gratis bagi warga desa.
    </p>

    <h2>⏰ Jam Operasional</h2>
    <p>
        Senin – Jumat: 08.00 – 16.00 WIB<br>
        Sabtu: 08.00 – 12.00 WIB<br>
        Minggu dan Hari Libur Nasional: Tutup
    </p>

    <h2>📍 Lokasi</h2>
    <p>
        Jl. Raya Kertasari No.123, Desa Kertasari, Kec. Pangkalan, Kabupaten Karawang, Jawa Barat
    </p>

    <h2>🧾 Layanan</h2>
    <ul>
        <li>Peminjaman dan pengembalian buku</li>
        <li>Pendaftaran anggota perpustakaan</li>
        <li>Akses internet dan komputer</li>
        <li>Kegiatan literasi dan pelatihan</li>
    </ul>

    <h2>☎️ Kontak</h2>
    <p>
        Telepon: (0267) 123456<br>
        Email: <a href="mailto:perpus.kertasari@gmail.com">perpus.kertasari@gmail.com</a>
    </p>
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

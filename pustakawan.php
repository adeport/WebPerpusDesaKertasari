<?php
include 'helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Petugas Perpustakaan</title>
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

        .staff-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .staff-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .staff-info h3 {
            margin: 0 0 6px;
        }

        .staff-info p {
            margin: 4px 0;
            color: #555;
        }

        @media (max-width: 768px) {
            main {
                padding: 20px;
            }
            .staff-card {
                flex-direction: column;
                text-align: center;
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
                margin-top: -20px;
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
    <?php if (current_user()): ?>
        <a href="profile.php">Profil</a>
        <?php if (is_admin()): ?>
            <a href="admin/dashboard.php">Dashboard Admin</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Daftar</a>
    <?php endif; ?>
    <a href="index.php">Beranda</a>
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
    <h1>Tim Pustakawan</h1>
</header>

<main>
    <div class="staff-card">
        <img src="css/images/staff1.jpg" alt="Petugas">
        <div class="staff-info">
            <h3>Sri Mulyani</h3>
            <p><strong>Jabatan:</strong> Kepala Perpustakaan</p>
            <p><strong>Email:</strong> sri.mulyani@desa.id</p>
            <p><strong>No. Telp:</strong> 0812-3456-7890</p>
        </div>
    </div>

    <div class="staff-card">
        <img src="css/images/staff2.jpg" alt="Petugas">
        <div class="staff-info">
            <h3>Rudi Hartono</h3>
            <p><strong>Jabatan:</strong> Petugas Administrasi</p>
            <p><strong>Email:</strong> rudi.hartono@desa.id</p>
            <p><strong>No. Telp:</strong> 0857-1122-3344</p>
        </div>
    </div>

    <div class="staff-card">
        <img src="css/images/staff3.jpg" alt="Petugas">
        <div class="staff-info">
            <h3>Lisa Anggraeni</h3>
            <p><strong>Jabatan:</strong> Pengelola Koleksi Buku</p>
            <p><strong>Email:</strong> lisa.anggraeni@desa.id</p>
            <p><strong>No. Telp:</strong> 0896-7788-6655</p>
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

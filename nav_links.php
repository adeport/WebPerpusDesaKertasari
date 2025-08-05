<!-- nav_links.php -->
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

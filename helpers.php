<?php
session_start();

// Konfigurasi database
$host = 'localhost';
$db = 'perpus_db';
$user = 'root';
$pass = ''; // ubah jika Anda pakai password

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("❌ Koneksi database gagal: " . $conn->connect_error);
}

// Mendapatkan user yang sedang login
function current_user() {
    return $_SESSION['user'] ?? null;
}

// Cek apakah user adalah admin
function is_admin() {
    return current_user() && current_user()['role'] === 'admin';
}

// Menampilkan notifikasi alert
function notify($msg) {
    echo "<script>alert('" . addslashes($msg) . "');</script>";
}

// Auto-return buku jika melewati tanggal kembali
function auto_return_books() {
    global $conn;
    $today = date('Y-m-d');

    // Ambil semua peminjaman yang lewat tanggal kembali dan masih "approved"
    $result = $conn->query("SELECT * FROM borrow_requests WHERE status = 'approved' AND return_date < '$today'");
    while ($row = $result->fetch_assoc()) {
        $borrow_id = $row['id'];
        $book_id = $row['book_id'];

        // Update status menjadi 'returned'
        $conn->query("UPDATE borrow_requests SET status = 'returned' WHERE id = $borrow_id");

        // Tambahkan stok buku
        $conn->query("UPDATE books SET stock = stock + 1 WHERE id = $book_id");

        // Ubah status buku menjadi available jika stok > 0
        $conn->query("UPDATE books SET status = 'available' WHERE id = $book_id AND stock > 0");
    }
}
?>

<?php 
include 'helpers.php'; 

if (!current_user()) die("Login dahulu.");

$user = current_user();
auto_return_books();

// Ambil dan validasi data dari POST
$book_id = $_POST['book_id'] ?? null;
$borrow_date = $_POST['borrow_date'] ?? null;
$return_date = $_POST['return_date'] ?? null;

if (!$book_id || !$borrow_date || !$return_date) {
    notify("Data tidak lengkap.");
    header("Location: index.php");
    exit;
}

// Cek stok buku
$stmt = $conn->prepare("SELECT stock FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$res = $stmt->get_result();
$book = $res->fetch_assoc();
$stmt->close();

if (!$book) {
    notify("❌ Buku tidak ditemukan.");
    header("Location: index.php");
    exit;
}

if ($book['stock'] <= 0) {
    notify("⚠️ Stok buku habis.");
    header("Location: index.php");
    exit;
}

// Simpan permintaan peminjaman
$stmt = $conn->prepare("INSERT INTO borrow_requests (user_id, book_id, borrow_date, return_date, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("iiss", $user['id'], $book_id, $borrow_date, $return_date);
$stmt->execute();
$stmt->close();

// Kurangi stok buku
$stmt = $conn->prepare("UPDATE books SET stock = stock - 1, status = IF(stock - 1 <= 0, 'borrowed', 'available') WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$stmt->close();

notify("✅ Permintaan peminjaman dikirim.");
header("Refresh: 1; url=index.php");
exit;
?>

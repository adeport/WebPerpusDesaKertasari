<?php
include 'helpers.php';
auto_return_books();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['identity']);
    $password = trim($_POST['password']);

    if (empty($input) || empty($password)) {
        notify("Semua field wajib diisi.");
    } else {
        // Cek apakah input berupa email atau username
        $query = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $query->bind_param('ss', $input, $input);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();

        // Cek password (gunakan password_verify jika pakai hash)
        if ($user && $user['password'] === $password) {
            $_SESSION['user'] = $user;
            $redirect = $user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            notify("Login gagal! Periksa kembali email/username dan password Anda.");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f2f4f8;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 400px;
            background: #fff;
            padding: 30px;
            margin: 60px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.06);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        form label {
            display: block;
            margin: 12px 0 5px;
        }
        form input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background-color: #2196F3;
            color: #fff;
            padding: 10px;
            border: none;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1976D2;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #2196F3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔐 Login</h2>
    <form method="post">
        <label for="identity">Email atau Username:</label>
        <input type="text" name="identity" id="identity" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>

</body>
</html>

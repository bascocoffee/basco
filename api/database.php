<?php
define('DB_HOST',     'localhost');
define('DB_USER',     'bast6842_coba');
define('DB_PASSWORD', 'M@lang1020');
define('DB_NAME',     'bast6842_coba');

function getConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$conn) {
        response(500, [
            "status"  => "error",
            "message" => "Koneksi database gagal: " . mysqli_connect_error()
        ]);
    }

    mysqli_set_charset($conn, "utf8");
    return $conn;
}
?>
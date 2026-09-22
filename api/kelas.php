<?php
ob_clean();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "database.php";

// ==========================
function response($code, $data) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

function getBody() {
    return json_decode(file_get_contents("php://input"), true);
}

function sanitize($conn, $value) {
    return mysqli_real_escape_string($conn, $value);
}

// ==========================
$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==========================
switch ($method) {

    // =====================
    // GET DATA KELAS
    // =====================
    case 'GET':

        // 🔹 GET BY ID
        if ($id > 0) {
            $sql = "SELECT id_kelas, nama_kelas 
                    FROM kelas_kel2 
                    WHERE id_kelas = $id 
                    LIMIT 1";
        }

        // 🔹 GET ALL
        else {
            $sql = "SELECT id_kelas, nama_kelas 
                    FROM kelas_kel2 
                    ORDER BY nama_kelas ASC";
        }

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            response(500, [
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        if ($id > 0 && count($data) === 0) {
            response(404, [
                "status" => "error",
                "message" => "Kelas tidak ditemukan"
            ]);
        }

        response(200, [
            "status" => "success",
            "data"   => $data
        ]);

    break;

    // =====================
    // TAMBAH KELAS
    // =====================
    case 'POST':

        $body = getBody();

        if (!$body || empty($body['nama_kelas'])) {
            response(400, [
                "status" => "error",
                "message" => "nama_kelas wajib diisi"
            ]);
        }

        $nama_kelas = sanitize($conn, $body['nama_kelas']);

        // cek duplikat
        $cek = mysqli_query($conn, "SELECT id_kelas FROM kelas_kel2 WHERE nama_kelas='$nama_kelas'");
        if (mysqli_num_rows($cek) > 0) {
            response(409, [
                "status" => "error",
                "message" => "Nama kelas sudah ada"
            ]);
        }

        $sql = "INSERT INTO kelas_kel2 (nama_kelas) VALUES ('$nama_kelas')";

        if (mysqli_query($conn, $sql)) {
            response(201, [
                "status"  => "success",
                "message" => "Kelas berhasil ditambahkan"
            ]);
        } else {
            response(500, [
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }

    break;

    // =====================
    // UPDATE KELAS
    // =====================
    case 'PUT':

        if ($id <= 0) {
            response(400, [
                "status" => "error",
                "message" => "ID wajib"
            ]);
        }

        $body = getBody();

        if (empty($body['nama_kelas'])) {
            response(400, [
                "status" => "error",
                "message" => "nama_kelas wajib diisi"
            ]);
        }

        $nama_kelas = sanitize($conn, $body['nama_kelas']);

        $sql = "UPDATE kelas_kel2 
                SET nama_kelas='$nama_kelas' 
                WHERE id_kelas=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, [
                "status"  => "success",
                "message" => "Kelas berhasil diupdate"
            ]);
        } else {
            response(500, [
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }

    break;

    // =====================
    // DELETE KELAS
    // =====================
    case 'DELETE':

        if ($id <= 0) {
            response(400, [
                "status" => "error",
                "message" => "ID wajib"
            ]);
        }

        // cek relasi siswa
        $cekSiswa = mysqli_query($conn, "SELECT id_siswa FROM siswa_kel2n WHERE id_kelas=$id LIMIT 1");
        if (mysqli_num_rows($cekSiswa) > 0) {
            response(409, [
                "status" => "error",
                "message" => "Kelas masih memiliki siswa"
            ]);
        }

        $sql = "DELETE FROM kelas_kel2 WHERE id_kelas=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, [
                "status"  => "success",
                "message" => "Kelas berhasil dihapus"
            ]);
        } else {
            response(500, [
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }

    break;

    default:
        response(405, [
            "status" => "error",
            "message" => "Method tidak diizinkan"
        ]);
}

mysqli_close($conn);
?>
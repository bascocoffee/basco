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
// FUNCTION HELPER
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
// INIT
// ==========================
$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==========================
// ROUTING
// ==========================
switch ($method) {

    // =====================
    // GET DATA SISWA
    // =====================
    case 'GET':

        $id_kelas = isset($_GET['id_kelas']) ? intval($_GET['id_kelas']) : 0;

    // 🔥 FILTER BY KELAS
    if ($id_kelas > 0) {

        $sql = "SELECT s.id_siswa, s.nama_siswa, s.id_kelas, k.nama_kelas
                FROM siswa_kel2n s
                LEFT JOIN kelas_kel2 k ON s.id_kelas = k.id_kelas
                WHERE s.id_kelas = $id_kelas
                ORDER BY s.nama_siswa ASC";
    }

    // 🔹 GET BY ID
    else if ($id > 0) {

        $sql = "SELECT s.id_siswa, s.nama_siswa, s.id_kelas, k.nama_kelas
                FROM siswa_kel2n s
                LEFT JOIN kelas_kel2 k ON s.id_kelas = k.id_kelas
                WHERE s.id_siswa = $id
                LIMIT 1";
    }

    // 🔹 GET ALL
    else {

        $sql = "SELECT s.id_siswa, s.nama_siswa, s.id_kelas, k.nama_kelas
                FROM siswa_kel2n s
                LEFT JOIN kelas_kel2 k ON s.id_kelas = k.id_kelas
                ORDER BY s.id_siswa ASC";
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

    response(200, [
        "status" => "success",
        "data"   => $data
    ]);

break;

    // =====================
    // TAMBAH SISWA
    // =====================
    case 'POST':

        $body = getBody();

        if (!$body) {
            response(400, [
                "status" => "error",
                "message" => "Body harus JSON"
            ]);
        }

        if (empty($body['nama_siswa']) || !isset($body['id_kelas'])) {
            response(400, [
                "status" => "error",
                "message" => "nama_siswa dan id_kelas wajib diisi"
            ]);
        }

        $nama_siswa = sanitize($conn, $body['nama_siswa']);
        $id_kelas   = intval($body['id_kelas']);

        // cek kelas
        $cek = mysqli_query($conn, "SELECT id_kelas FROM kelas_kel2 WHERE id_kelas=$id_kelas");
        if (mysqli_num_rows($cek) === 0) {
            response(400, [
                "status" => "error",
                "message" => "Kelas tidak ditemukan"
            ]);
        }

        $sql = "INSERT INTO siswa_kel2n (nama_siswa, id_kelas)
                VALUES ('$nama_siswa', $id_kelas)";

        if (mysqli_query($conn, $sql)) {
            response(201, [
                "status"  => "success",
                "message" => "Siswa berhasil ditambahkan"
            ]);
        } else {
            response(500, [
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }
        break;

    // =====================
    // UPDATE SISWA
    // =====================
    case 'PUT':

        case 'PUT':

    if ($id <= 0) {
        response(400, [
            "status" => "error",
            "message" => "ID wajib"
        ]);
    }

    $body = getBody();

    if (empty($body['nama_siswa']) || !isset($body['id_kelas'])) {
        response(400, [
            "status" => "error",
            "message" => "nama_siswa dan id_kelas wajib diisi"
        ]);
    }

    // ✅ FIX tabel
    $cek = mysqli_query($conn, "SELECT id_siswa FROM siswa_kel2n WHERE id_siswa=$id");
    if (mysqli_num_rows($cek) === 0) {
        response(404, [
            "status" => "error",
            "message" => "Siswa tidak ditemukan"
        ]);
    }

    $nama_siswa = sanitize($conn, $body['nama_siswa']);
    $id_kelas   = intval($body['id_kelas']);

    // ✅ FIX tabel
    $cekKelas = mysqli_query($conn, "SELECT id_kelas FROM kelas_kel2 WHERE id_kelas=$id_kelas");
    if (mysqli_num_rows($cekKelas) === 0) {
        response(400, [
            "status" => "error",
            "message" => "Kelas tidak ditemukan"
        ]);
    }

    $sql = "UPDATE siswa_kel2n 
            SET nama_siswa='$nama_siswa', id_kelas=$id_kelas 
            WHERE id_siswa=$id";

    if (mysqli_query($conn, $sql)) {
        response(200, [
            "status"  => "success",
            "message" => "Siswa berhasil diupdate"
        ]);
    } else {
        response(500, [
            "status" => "error",
            "message" => mysqli_error($conn)
        ]);
    }

break;

    // =====================
    // DELETE SISWA
    // =====================
    case 'DELETE':

        if ($id <= 0) {
            response(400, [
                "status" => "error",
                "message" => "ID wajib"
            ]);
        }

        $cek = mysqli_query($conn, "SELECT id_siswa FROM siswa_kel2n WHERE id_siswa=$id");
        if (mysqli_num_rows($cek) === 0) {
            response(404, [
                "status" => "error",
                "message" => "Siswa tidak ditemukan"
            ]);
        }

        $sql = "DELETE FROM siswa_kel2n WHERE id_siswa=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, [
                "status"  => "success",
                "message" => "Siswa berhasil dihapus"
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
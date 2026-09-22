<?php
ob_clean();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "database.php";

// ==========================
// HELPER
// ==========================
function response($code, $data) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

function getBody() {
    return json_decode(file_get_contents("php://input"), true);
}

function sanitize($conn, $val) {
    return mysqli_real_escape_string($conn, $val);
}

// ==========================
$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==========================
switch ($method) {

    // =====================
    // GET
    // =====================
    case 'GET':

        $id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;
        $tanggal  = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

        $where = "WHERE 1=1";

        if ($id_siswa > 0) {
            $where .= " AND d.id_siswa = $id_siswa";
        }

        if (!empty($tanggal)) {
            $tanggal = sanitize($conn, $tanggal);
            $where .= " AND d.tanggal = '$tanggal'";
        }

        if ($id > 0) {
            $where .= " AND d.id_dispen = $id";
        }

        $sql = "SELECT d.*, s.nama_siswa, k.nama_kelas
                FROM dispensasi_kel2 d
                LEFT JOIN siswa_kel2n s ON d.id_siswa = s.id_siswa
                LEFT JOIN kelas_kel2 k ON s.id_kelas = k.id_kelas
                $where
                ORDER BY d.created_at DESC";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            response(500, ["status"=>"error","message"=>mysqli_error($conn)]);
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        response(200, ["status"=>"success","data"=>$data]);
        break;

    // =====================
    // POST (CREATE)
    // =====================
    case 'POST':

        $body = getBody();

        if (!$body) {
            response(400, ["status"=>"error","message"=>"Body harus JSON"]);
        }

        if (
            empty($body['id_siswa']) ||
            empty($body['jenis_dispen']) ||
            empty($body['tanggal']) ||
            empty($body['jam_keluar'])
        ) {
            response(400, [
                "status"=>"error",
                "message"=>"Field wajib: id_siswa, jenis_dispen, tanggal, jam_keluar"
            ]);
        }

        $id_siswa = intval($body['id_siswa']);

        // ✅ cek siswa valid
        $cek = mysqli_query($conn, "SELECT id_siswa FROM siswa_kel2n WHERE id_siswa=$id_siswa");
        if (mysqli_num_rows($cek) === 0) {
            response(400, ["status"=>"error","message"=>"Siswa tidak ditemukan"]);
        }

        $jenis      = sanitize($conn, $body['jenis_dispen']);
        $keterangan = sanitize($conn, $body['keterangan'] ?? '');
        $tanggal    = sanitize($conn, $body['tanggal']);
        $jam_keluar = sanitize($conn, $body['jam_keluar']);

        $jam_kembali = !empty($body['jam_kembali'])
            ? "'" . sanitize($conn, $body['jam_kembali']) . "'"
            : "NULL";

        $sql = "INSERT INTO dispensasi_kel2 
                (id_siswa, jenis_dispen, keterangan, tanggal, jam_keluar, jam_kembali, status)
                VALUES 
                ($id_siswa, '$jenis', '$keterangan', '$tanggal', '$jam_keluar', $jam_kembali, 'menunggu')";

        if (mysqli_query($conn, $sql)) {
            response(201, [
                "status"=>"success",
                "message"=>"Dispensasi berhasil dibuat",
                "id_dispen"=>mysqli_insert_id($conn)
            ]);
        } else {
            response(500, ["status"=>"error","message"=>mysqli_error($conn)]);
        }
        break;

    // =====================
    // PUT (UPDATE)
    // =====================
    case 'PUT':

        if ($id <= 0) {
            response(400, ["status"=>"error","message"=>"ID wajib"]);
        }

        $body = getBody();

        $cek = mysqli_query($conn, "SELECT id_dispen FROM dispensasi_kel2 WHERE id_dispen=$id");
        if (mysqli_num_rows($cek) === 0) {
            response(404, ["status"=>"error","message"=>"Data tidak ditemukan"]);
        }

        $fields = [];

        if (isset($body['status'])) {
            $status = sanitize($conn, $body['status']);
            $fields[] = "status='$status'";
        }

        if (isset($body['jam_kembali'])) {
            if ($body['jam_kembali'] === null || $body['jam_kembali'] === '') {
                $fields[] = "jam_kembali=NULL";
            } else {
                $jam = sanitize($conn, $body['jam_kembali']);
                $fields[] = "jam_kembali='$jam'";
            }
        }

        if (isset($body['keterangan'])) {
            $ket = sanitize($conn, $body['keterangan']);
            $fields[] = "keterangan='$ket'";
        }

        if (empty($fields)) {
            response(400, ["status"=>"error","message"=>"Tidak ada data diupdate"]);
        }

        $sql = "UPDATE dispensasi_kel2 SET " . implode(",", $fields) . " WHERE id_dispen=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, ["status"=>"success","message"=>"Berhasil update"]);
        } else {
            response(500, ["status"=>"error","message"=>mysqli_error($conn)]);
        }
        break;

    // =====================
    // DELETE
    // =====================
    case 'DELETE':

        if ($id <= 0) {
            response(400, ["status"=>"error","message"=>"ID wajib"]);
        }

        $cek = mysqli_query($conn, "SELECT id_dispen FROM dispensasi_kel2 WHERE id_dispen=$id");
        if (mysqli_num_rows($cek) === 0) {
            response(404, ["status"=>"error","message"=>"Data tidak ditemukan"]);
        }

        $sql = "DELETE FROM dispensasi_kel2 WHERE id_dispen=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, ["status"=>"success","message"=>"Data dihapus"]);
        } else {
            response(500, ["status"=>"error","message"=>mysqli_error($conn)]);
        }

        break;

    default:
        response(405, ["status"=>"error","message"=>"Method tidak diizinkan"]);
}

mysqli_close($conn);
?>
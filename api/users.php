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
$action = isset($_GET['action']) ? $_GET['action'] : '';

$VALID_ROLES = ['adminpiket', 'gurukelas'];

// ==========================
// ROUTING
// ==========================
switch ($method) {

    // =====================
    // LOGIN
    // =====================
    case 'POST':
        if ($action === 'login') {

            $body = getBody();

            if (!$body) {
                response(400, [
                    "status" => "error",
                    "message" => "Body harus JSON"
                ]);
            }

            if (empty($body['username']) || empty($body['password'])) {
                response(400, [
                    "status" => "error",
                    "message" => "Username dan password wajib diisi"
                ]);
            }

            $username = sanitize($conn, $body['username']);
            $password = sanitize($conn, $body['password']);

            $sql = "SELECT id_user, username, password, role 
                    FROM users_kel2 
                    WHERE username = '$username' 
                    LIMIT 1";

            $result = mysqli_query($conn, $sql);

            if (!$result) {
                response(500, [
                    "status" => "error",
                    "message" => mysqli_error($conn)
                ]);
            }

            if (mysqli_num_rows($result) === 0) {
                response(401, [
                    "status" => "error",
                    "message" => "Username atau password salah"
                ]);
            }

            $user = mysqli_fetch_assoc($result);

            // 🔥 TANPA HASH
            if ($password !== $user['password']) {
                response(401, [
                    "status" => "error",
                    "message" => "Username atau password salah"
                ]);
            }

            unset($user['password']);

            response(200, [
                "status"  => "success",
                "message" => "Login berhasil",
                "data"    => [
                    "user" => $user
                ]
            ]);
        }

        // =====================
        // REGISTER USER
        // =====================
        if ($action === 'register') {

            $body = getBody();

            if (empty($body['username']) || empty($body['password']) || empty($body['role'])) {
                response(400, [
                    "status" => "error",
                    "message" => "Username, password, dan role wajib diisi"
                ]);
            }

            if (!in_array($body['role'], $VALID_ROLES)) {
                response(400, [
                    "status" => "error",
                    "message" => "Role tidak valid"
                ]);
            }

            $username = sanitize($conn, $body['username']);
            $password = sanitize($conn, $body['password']);
            $role     = sanitize($conn, $body['role']);

            // cek duplikat
            $cek = mysqli_query($conn, "SELECT id_user FROM users_kel2 WHERE username='$username'");
            if (mysqli_num_rows($cek) > 0) {
                response(409, [
                    "status" => "error",
                    "message" => "Username sudah digunakan"
                ]);
            }

            $sql = "INSERT INTO users_kel2 (username, password, role)
                    VALUES ('$username', '$password', '$role')";

            if (mysqli_query($conn, $sql)) {
                response(201, [
                    "status" => "success",
                    "message" => "User berhasil ditambahkan"
                ]);
            } else {
                response(500, [
                    "status" => "error",
                    "message" => mysqli_error($conn)
                ]);
            }
        }

        response(400, ["status"=>"error","message"=>"Action tidak valid"]);
        break;

    // =====================
    // GET USER
    // =====================
    case 'GET':
        if ($id > 0) {
            $sql = "SELECT id_user, username, role FROM users_kel2 WHERE id_user=$id";
        } else {
            $sql = "SELECT id_user, username, role FROM users_kel2 ORDER BY id_user ASC";
        }

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            response(500, ["status"=>"error","message"=>mysqli_error($conn)]);
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
    // DELETE USER
    // =====================
    case 'DELETE':
        if ($id <= 0) {
            response(400, ["status"=>"error","message"=>"ID wajib"]);
        }

        $sql = "DELETE FROM users_kel2 WHERE id_user=$id";

        if (mysqli_query($conn, $sql)) {
            response(200, [
                "status"=>"success",
                "message"=>"User berhasil dihapus"
            ]);
        } else {
            response(500, [
                "status"=>"error",
                "message"=>mysqli_error($conn)
            ]);
        }
        break;

    default:
        response(405, ["status"=>"error","message"=>"Method tidak diizinkan"]);
}

mysqli_close($conn);
?>
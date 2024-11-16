<?php
// Permitir acesso de qualquer origem
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Dados de conexão
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'smb';

// Conectar ao banco de dados MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variável de email
$email = '';

// Recebe os dados JSON da requisição POST ou o parâmetro GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
}

// Verifica se o email foi fornecido
if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email não fornecido"]);
    exit;
}

// Consulta SQL para verificar a existência do email
$sql = "SELECT id FROM `usuário` WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Retorna o resultado como JSON
if ($result->num_rows > 0) {
    echo json_encode(["success" => true, "existe" => true, "message" => "Email encontrado"]);
} else {
    echo json_encode(["success" => true, "existe" => false, "message" => "Email não encontrado"]);
}

// Fecha a conexão com o banco
$stmt->close();
$conn->close();
?>

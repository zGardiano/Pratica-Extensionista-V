<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/criar_cupom.php');
    exit;
}

$titulo = trim($_POST['titulo']);
$percentual = floatval($_POST['percentual']);
$data_inicio = $_POST['data_inicio'];
$data_termino = $_POST['data_termino'];
$cnpj_comercio = $_SESSION['user_id']; // Já é VARCHAR do banco

if (empty($titulo) || $percentual <= 0 || $percentual > 100) {
    header('Location: ../pages/criar_cupom.php?error=' . urlencode('Dados inválidos'));
    exit;
}

if (strtotime($data_inicio) > strtotime($data_termino)) {
    header('Location: ../pages/criar_cupom.php?error=' . urlencode('A data de início deve ser anterior à data de término'));
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$num_cupom = gerarCodigoCupom();

$stmt = $conn->prepare("SELECT num_cupom FROM CUPOM WHERE num_cupom = ?");
$stmt->execute([$num_cupom]);

while ($stmt->fetch()) {
    $num_cupom = gerarCodigoCupom();
    $stmt->execute([$num_cupom]);
}

try {
    $stmt = $conn->prepare("INSERT INTO CUPOM (num_cupom, tit_cupom, cnpj_comercio, 
                            dta_emissao_cupom, dta_inicio_cupom, dta_termino_cupom, per_desc_cupom) 
                            VALUES (?, ?, ?, CURDATE(), ?, ?, ?)");
    
    $stmt->execute([
        $num_cupom,
        $titulo,
        $cnpj_comercio,
        $data_inicio,
        $data_termino,
        $percentual
    ]);
    
    header('Location: ../pages/dashboard_comercio.php?success=' . urlencode('Cupom criado com sucesso! Código: ' . $num_cupom));
    exit;
    
} catch (PDOException $e) {
    header('Location: ../pages/criar_cupom.php?error=' . urlencode('Erro ao criar cupom. Tente novamente.'));
    exit;
}
?>

<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'associado') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/listar_cupons_associado.php');
    exit;
}

$num_cupom = $_POST['num_cupom'];
$cpf_associado = $_SESSION['user_id']; // Já é VARCHAR do banco

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT num_cupom, dta_inicio_cupom, dta_termino_cupom 
                        FROM CUPOM 
                        WHERE num_cupom = ? 
                        AND dta_inicio_cupom <= CURDATE() 
                        AND dta_termino_cupom >= CURDATE()");
$stmt->execute([$num_cupom]);
$cupom = $stmt->fetch();

if (!$cupom) {
    header('Location: ../pages/listar_cupons_associado.php?error=' . urlencode('Cupom inválido ou expirado'));
    exit;
}

$stmt = $conn->prepare("SELECT id_cupom_associado FROM CUPOM_ASSOCIADO 
                        WHERE num_cupom = ? AND cpf_associado = ?");
$stmt->execute([$num_cupom, $cpf_associado]);

if ($stmt->fetch()) {
    header('Location: ../pages/listar_cupons_associado.php?error=' . urlencode('Você já reservou este cupom'));
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO CUPOM_ASSOCIADO (num_cupom, cpf_associado, dta_cupom_associado) 
                            VALUES (?, ?, CURDATE())");
    $stmt->execute([$num_cupom, $cpf_associado]);
    
    header('Location: ../pages/dashboard_associado.php?success=' . urlencode('Cupom reservado com sucesso!'));
    exit;
    
} catch (PDOException $e) {
    header('Location: ../pages/listar_cupons_associado.php?error=' . urlencode('Erro ao reservar cupom'));
    exit;
}
?>

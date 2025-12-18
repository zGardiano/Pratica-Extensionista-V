<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/validar_cupom.php');
    exit;
}

$id_cupom_associado = intval($_POST['id_cupom_associado']);
$cnpj = $_SESSION['user_id']; // Já é VARCHAR

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT ca.id_cupom_associado, ca.dta_uso_cupom_associado, c.num_cupom
                        FROM CUPOM_ASSOCIADO ca
                        INNER JOIN CUPOM c ON ca.num_cupom = c.num_cupom
                        WHERE ca.id_cupom_associado = ? AND c.cnpj_comercio = ?");
$stmt->execute([$id_cupom_associado, $cnpj]);
$cupom = $stmt->fetch();

if (!$cupom) {
    header('Location: ../pages/validar_cupom.php?error=' . urlencode('Cupom não encontrado'));
    exit;
}

if ($cupom['dta_uso_cupom_associado']) {
    header('Location: ../pages/validar_cupom.php?error=' . urlencode('Cupom já foi utilizado'));
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE CUPOM_ASSOCIADO SET dta_uso_cupom_associado = CURDATE() 
                            WHERE id_cupom_associado = ?");
    $stmt->execute([$id_cupom_associado]);
    
    header('Location: ../pages/dashboard_comercio.php?success=' . urlencode('Cupom validado com sucesso!'));
    exit;
    
} catch (PDOException $e) {
    header('Location: ../pages/validar_cupom.php?error=' . urlencode('Erro ao validar cupom'));
    exit;
}
?>

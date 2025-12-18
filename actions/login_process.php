<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$documento = preg_replace('/[^0-9]/', '', $_POST['documento']);
$senha = $_POST['senha'];

$db = new Database();
$conn = $db->getConnection();

if (strlen($documento) == 11) {
    // Login de Associado
    if (!validarCPF($documento)) {
        header('Location: ../index.php?error=' . urlencode('CPF inválido'));
        exit;
    }
    
    $stmt = $conn->prepare("SELECT cpf_associado, nom_associado, sen_associado 
                            FROM ASSOCIADO WHERE cpf_associado = ?");
    $stmt->execute([$documento]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($senha, $user['sen_associado'])) {
        $_SESSION['user_id'] = $user['cpf_associado'];
        $_SESSION['user_name'] = $user['nom_associado'];
        $_SESSION['user_type'] = 'associado';
        header('Location: ../pages/dashboard_associado.php');
        exit;
    }
    
} elseif (strlen($documento) == 14) {
    // Login de Comerciante
    if (!validarCNPJ($documento)) {
        header('Location: ../index.php?error=' . urlencode('CNPJ inválido'));
        exit;
    }
    
    $stmt = $conn->prepare("SELECT cnpj_comercio, nom_fantasia_comercio, raz_social_comercio, sen_comercio 
                            FROM COMERCIO WHERE cnpj_comercio = ?");
    $stmt->execute([$documento]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($senha, $user['sen_comercio'])) {
        $_SESSION['user_id'] = $user['cnpj_comercio'];
        // Se nome fantasia vazio, usar razão social
        $_SESSION['user_name'] = !empty($user['nom_fantasia_comercio']) 
                                  ? $user['nom_fantasia_comercio'] 
                                  : $user['raz_social_comercio'];
        $_SESSION['user_type'] = 'comercio';
        header('Location: ../pages/dashboard_comercio.php');
        exit;
    }
}

header('Location: ../index.php?error=' . urlencode('Documento ou senha inválidos'));
exit;
?>

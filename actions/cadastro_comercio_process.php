<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cadastro_comercio.php');
    exit;
}

$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);
$razao_social = trim($_POST['razao_social']);
$nome_fantasia = trim($_POST['nome_fantasia'] ?? '');

// Se nome fantasia vazio, usar razão social
if (empty($nome_fantasia)) {
    $nome_fantasia = $razao_social;
}

$categoria = intval($_POST['categoria']);
$endereco = trim($_POST['endereco'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$cep = preg_replace('/[^0-9]/', '', $_POST['cep'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$uf = strtoupper(trim($_POST['uf'] ?? ''));
$contato = trim($_POST['contato'] ?? '');
$email = trim($_POST['email']);
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

if (!validarCNPJ($cnpj)) {
    header('Location: ../pages/cadastro_comercio.php?error=' . urlencode('CNPJ inválido'));
    exit;
}

if ($senha !== $confirmar_senha) {
    header('Location: ../pages/cadastro_comercio.php?error=' . urlencode('As senhas não coincidem'));
    exit;
}

if (strlen($senha) < 6) {
    header('Location: ../pages/cadastro_comercio.php?error=' . urlencode('A senha deve ter no mínimo 6 caracteres'));
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT cnpj_comercio FROM COMERCIO WHERE cnpj_comercio = ? OR email_comercio = ?");
$stmt->execute([$cnpj, $email]);

if ($stmt->fetch()) {
    header('Location: ../pages/cadastro_comercio.php?error=' . urlencode('CNPJ ou e-mail já cadastrado'));
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO COMERCIO (cnpj_comercio, id_categoria, raz_social_comercio, 
                            nom_fantasia_comercio, end_comercio, bai_comercio, cep_comercio, 
                            cid_comercio, uf_comercio, con_comercio, email_comercio, sen_comercio) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $cnpj,
        $categoria,
        $razao_social,
        $nome_fantasia,
        $endereco,
        $bairro,
        $cep,
        $cidade,
        $uf,
        $contato,
        $email,
        $senha_hash
    ]);
    
    header('Location: ../index.php?success=' . urlencode('Cadastro realizado com sucesso!'));
    exit;
    
} catch (PDOException $e) {
    header('Location: ../pages/cadastro_comercio.php?error=' . urlencode('Erro ao cadastrar'));
    exit;
}
?>

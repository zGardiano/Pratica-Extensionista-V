<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cadastro_associado.php');
    exit;
}

$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$nome = trim($_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];
$endereco = trim($_POST['endereco'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$cep = preg_replace('/[^0-9]/', '', $_POST['cep'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$uf = strtoupper(trim($_POST['uf'] ?? ''));
$celular = trim($_POST['celular'] ?? '');
$email = trim($_POST['email']);
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// Validações
if (!validarCPF($cpf)) {
    header('Location: ../pages/cadastro_associado.php?error=' . urlencode('CPF inválido'));
    exit;
}

if ($senha !== $confirmar_senha) {
    header('Location: ../pages/cadastro_associado.php?error=' . urlencode('As senhas não coincidem'));
    exit;
}

if (strlen($senha) < 6) {
    header('Location: ../pages/cadastro_associado.php?error=' . urlencode('A senha deve ter no mínimo 6 caracteres'));
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verificar se CPF ou e-mail já existe
$stmt = $conn->prepare("SELECT cpf_associado FROM ASSOCIADO WHERE cpf_associado = ? OR email_associado = ?");
$stmt->execute([$cpf, $email]);

if ($stmt->fetch()) {
    header('Location: ../pages/cadastro_associado.php?error=' . urlencode('CPF ou e-mail já cadastrado'));
    exit;
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO ASSOCIADO (cpf_associado, nom_associado, dtn_associado, 
                            end_associado, bai_associado, cep_associado, cid_associado, 
                            uf_associado, cel_associado, email_associado, sen_associado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $cpf,
        $nome,
        $data_nascimento,
        $endereco,
        $bairro,
        $cep,
        $cidade,
        $uf,
        $celular,
        $email,
        $senha_hash
    ]);
    
    header('Location: ../index.php?success=' . urlencode('Cadastro realizado com sucesso!'));
    exit;
    
} catch (PDOException $e) {
    header('Location: ../pages/cadastro_associado.php?error=' . urlencode('Erro ao cadastrar. Tente novamente.'));
    exit;
}
?>

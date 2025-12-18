<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$cupom_info = null;

if (isset($_GET['codigo'])) {
    $codigo = strtoupper(trim($_GET['codigo']));
    $cnpj = $_SESSION['user_id'];
    
    // Buscar informações do cupom
    $stmt = $conn->prepare("SELECT c.num_cupom, c.tit_cupom, c.per_desc_cupom, c.dta_termino_cupom,
                            ca.id_cupom_associado, ca.cpf_associado, ca.dta_cupom_associado, 
                            ca.dta_uso_cupom_associado, a.nom_associado
                            FROM CUPOM c
                            INNER JOIN CUPOM_ASSOCIADO ca ON c.num_cupom = ca.num_cupom
                            INNER JOIN ASSOCIADO a ON ca.cpf_associado = a.cpf_associado
                            WHERE c.num_cupom = ? AND c.cnpj_comercio = ?");
    $stmt->execute([$codigo, $cnpj]);
    $cupom_info = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Cupom</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Validar Cupom</h1>
        <a href="dashboard_comercio.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <div class="container">
        <div class="form-box">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form method="GET" action="">
                <div class="form-group">
                    <label>Código do Cupom:</label>
                    <input type="text" name="codigo" required maxlength="12" 
                           placeholder="Digite o código de 12 caracteres"
                           value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>"
                           style="text-transform: uppercase;">
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
            
            <?php if ($cupom_info): ?>
                <div class="cupom-validacao">
                    <h3><?php echo htmlspecialchars($cupom_info['tit_cupom']); ?></h3>
                    <p><strong>Desconto:</strong> <?php echo number_format($cupom_info['per_desc_cupom'], 2, ',', '.'); ?>%</p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cupom_info['nom_associado']); ?></p>
                    <p><strong>Reservado em:</strong> <?php echo date('d/m/Y', strtotime($cupom_info['dta_cupom_associado'])); ?></p>
                    <p><strong>Válido até:</strong> <?php echo date('d/m/Y', strtotime($cupom_info['dta_termino_cupom'])); ?></p>
                    
                    <?php if ($cupom_info['dta_uso_cupom_associado']): ?>
                        <div class="alert alert-error">
                            <strong>CUPOM JÁ UTILIZADO</strong><br>
                            Data de uso: <?php echo date('d/m/Y', strtotime($cupom_info['dta_uso_cupom_associado'])); ?>
                        </div>
                    <?php elseif ($cupom_info['dta_termino_cupom'] < date('Y-m-d')): ?>
                        <div class="alert alert-error">
                            <strong>CUPOM VENCIDO</strong>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <strong>CUPOM VÁLIDO</strong>
                        </div>
                        <form action="../actions/validar_cupom_process.php" method="POST">
                            <input type="hidden" name="id_cupom_associado" value="<?php echo $cupom_info['id_cupom_associado']; ?>">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Confirma o uso deste cupom?')">
                                Marcar como Utilizado
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php elseif (isset($_GET['codigo'])): ?>
                <div class="alert alert-error">
                    Cupom não encontrado ou não pertence ao seu estabelecimento.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

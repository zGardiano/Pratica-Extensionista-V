<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$cnpj = $_SESSION['user_id']; // Já é VARCHAR

// Estatísticas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM CUPOM WHERE cnpj_comercio = ?");
$stmt->execute([$cnpj]);
$total_cupons = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(DISTINCT ca.id_cupom_associado) as total 
                        FROM CUPOM_ASSOCIADO ca 
                        INNER JOIN CUPOM c ON ca.num_cupom = c.num_cupom 
                        WHERE c.cnpj_comercio = ? AND ca.dta_uso_cupom_associado IS NOT NULL");
$stmt->execute([$cnpj]);
$cupons_utilizados = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(DISTINCT ca.id_cupom_associado) as total 
                        FROM CUPOM_ASSOCIADO ca 
                        INNER JOIN CUPOM c ON ca.num_cupom = c.num_cupom 
                        WHERE c.cnpj_comercio = ? AND ca.dta_uso_cupom_associado IS NULL");
$stmt->execute([$cnpj]);
$cupons_reservados = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comerciante</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <a href="../actions/logout.php" class="btn btn-secondary">Sair</a>
    </div>
    
    <div class="container">
        <div class="dashboard">
            <h2>Painel do Comerciante</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo $total_cupons; ?></h3>
                    <p>Cupons Criados</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $cupons_reservados; ?></h3>
                    <p>Cupons Reservados</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $cupons_utilizados; ?></h3>
                    <p>Cupons Utilizados</p>
                </div>
            </div>
            
            <div class="menu-cards">
                <a href="criar_cupom.php" class="card">
                    <h3>Criar Novo Cupom</h3>
                    <p>Gere cupons de desconto para seus clientes</p>
                </a>
                
                <a href="listar_cupons_comercio.php" class="card">
                    <h3>Meus Cupons</h3>
                    <p>Visualize e gerencie seus cupons</p>
                </a>
                
                <a href="validar_cupom.php" class="card">
                    <h3>Validar Cupom</h3>
                    <p>Marque cupons como utilizados</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

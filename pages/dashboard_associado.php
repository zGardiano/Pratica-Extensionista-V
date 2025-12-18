<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'associado') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Associado</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <a href="../actions/logout.php" class="btn btn-secondary">Sair</a>
    </div>
    
    <div class="container">
        <div class="dashboard">
            <h2>Meu Painel</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div class="menu-cards">
                <a href="listar_cupons_associado.php" class="card">
                    <h3>Cupons Disponíveis</h3>
                    <p>Veja e reserve cupons de desconto</p>
                </a>
                
                <a href="listar_cupons_associado.php?status=reservados" class="card">
                    <h3>Meus Cupons Reservados</h3>
                    <p>Cupons que você já reservou</p>
                </a>
                
                <a href="listar_cupons_associado.php?status=utilizados" class="card">
                    <h3>Cupons Utilizados</h3>
                    <p>Histórico de cupons usados</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

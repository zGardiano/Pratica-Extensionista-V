<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'associado') {
        header('Location: pages/dashboard_associado.php');
    } else {
        header('Location: pages/dashboard_comercio.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cupons - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>Sistema de Cupons</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="actions/login_process.php" method="POST">
                <div class="form-group">
                    <label for="documento">CPF ou CNPJ:</label>
                    <input type="text" id="documento" name="documento" required 
                           placeholder="Digite apenas números">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            
            <div class="links">
                <a href="pages/cadastro_associado.php">Cadastrar como Associado</a>
                <a href="pages/cadastro_comercio.php">Cadastrar como Comerciante</a>
            </div>
        </div>
    </div>
</body>
</html>

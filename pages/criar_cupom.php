<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Cupom</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Criar Novo Cupom</h1>
        <a href="dashboard_comercio.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <div class="container">
        <div class="form-box">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form action="../actions/criar_cupom_process.php" method="POST">
                <div class="form-group">
                    <label>Título do Cupom:*</label>
                    <input type="text" name="titulo" required maxlength="25" 
                           placeholder="Ex: Black Friday 2024">
                </div>
                
                <div class="form-group">
                    <label>Percentual de Desconto (%):*</label>
                    <input type="number" name="percentual" required 
                           min="0" max="100" step="0.01" 
                           placeholder="Ex: 15.50">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Data de Início:*</label>
                        <input type="date" name="data_inicio" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Data de Término:*</label>
                        <input type="date" name="data_termino" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="info-box">
                    <p><strong>Atenção:</strong> O código do cupom será gerado automaticamente pelo sistema.</p>
                </div>
                
                <button type="submit" class="btn btn-primary">Criar Cupom</button>
            </form>
        </div>
    </div>
    
    <script>
        // Validação de data no frontend
        document.querySelector('input[name="data_inicio"]').addEventListener('change', function() {
            document.querySelector('input[name="data_termino"]').min = this.value;
        });
    </script>
</body>
</html>

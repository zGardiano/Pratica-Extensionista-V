<?php
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT id_categoria, nom_categoria FROM CATEGORIA ORDER BY nom_categoria");
$categorias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Comércio</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h1>Cadastro de Comércio</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form action="../actions/cadastro_comercio_process.php" method="POST">
                <div class="form-group">
                    <label>CNPJ:*</label>
                    <input type="text" name="cnpj" required placeholder="00.000.000/0000-00">
                </div>
                
                <div class="form-group">
                    <label>Razão Social:*</label>
                    <input type="text" name="razao_social" required maxlength="50">
                </div>
                
                <div class="form-group">
                    <label>Nome Fantasia:</label>
                    <input type="text" name="nome_fantasia" maxlength="30">
                </div>
                
                <div class="form-group">
                    <label>Categoria:*</label>
                    <select name="categoria" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nom_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" maxlength="30">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Bairro:</label>
                        <input type="text" name="bairro" maxlength="30">
                    </div>
                    
                    <div class="form-group">
                        <label>CEP:</label>
                        <input type="text" name="cep" maxlength="8">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Cidade:</label>
                        <input type="text" name="cidade" maxlength="40">
                    </div>
                    
                    <div class="form-group">
                        <label>UF:</label>
                        <input type="text" name="uf" maxlength="2" placeholder="SP">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Contato:</label>
                    <input type="text" name="contato" maxlength="15">
                </div>
                
                <div class="form-group">
                    <label>E-mail:*</label>
                    <input type="email" name="email" required maxlength="50">
                </div>
                
                <div class="form-group">
                    <label>Senha:*</label>
                    <input type="password" name="senha" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Confirmar Senha:*</label>
                    <input type="password" name="confirmar_senha" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="../index.php" class="btn btn-secondary">Voltar</a>
            </form>
        </div>
    </div>
</body>
</html>

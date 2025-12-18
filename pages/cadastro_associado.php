<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Associado</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h1>Cadastro de Associado</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form action="../actions/cadastro_associado_process.php" method="POST">
                <div class="form-group">
                    <label>CPF:*</label>
                    <input type="text" name="cpf" required placeholder="000.000.000-00">
                </div>
                
                <div class="form-group">
                    <label>Nome Completo:*</label>
                    <input type="text" name="nome" required maxlength="40">
                </div>
                
                <div class="form-group">
                    <label>Data de Nascimento:*</label>
                    <input type="date" name="data_nascimento" required>
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
                        <input type="text" name="cep" maxlength="8" placeholder="00000000">
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
                    <label>Celular:</label>
                    <input type="text" name="celular" maxlength="15" placeholder="(00) 00000-0000">
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

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'associado') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$status = $_GET['status'] ?? 'disponiveis';
$cpf = $_SESSION['user_id'];

if ($status === 'disponiveis') {
    // Cupons ativos e não reservados pelo usuário
    $sql = "SELECT c.num_cupom, c.tit_cupom, c.per_desc_cupom, c.dta_inicio_cupom, 
                   c.dta_termino_cupom, co.nom_fantasia_comercio, cat.nom_categoria
            FROM CUPOM c
            INNER JOIN COMERCIO co ON c.cnpj_comercio = co.cnpj_comercio
            INNER JOIN CATEGORIA cat ON co.id_categoria = cat.id_categoria
            WHERE c.dta_inicio_cupom <= CURDATE() 
            AND c.dta_termino_cupom >= CURDATE()
            AND c.num_cupom NOT IN (SELECT num_cupom FROM CUPOM_ASSOCIADO WHERE cpf_associado = ?)
            ORDER BY c.dta_termino_cupom";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cpf]);
    $titulo = "Cupons Disponíveis";
    
} elseif ($status === 'reservados') {
    // Cupons reservados e não utilizados
    $sql = "SELECT c.num_cupom, c.tit_cupom, c.per_desc_cupom, c.dta_inicio_cupom, 
                   c.dta_termino_cupom, co.nom_fantasia_comercio, cat.nom_categoria,
                   ca.dta_cupom_associado
            FROM CUPOM_ASSOCIADO ca
            INNER JOIN CUPOM c ON ca.num_cupom = c.num_cupom
            INNER JOIN COMERCIO co ON c.cnpj_comercio = co.cnpj_comercio
            INNER JOIN CATEGORIA cat ON co.id_categoria = cat.id_categoria
            WHERE ca.cpf_associado = ? AND ca.dta_uso_cupom_associado IS NULL
            ORDER BY c.dta_termino_cupom";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cpf]);
    $titulo = "Meus Cupons Reservados";
    
} else {
    // Cupons utilizados
    $sql = "SELECT c.num_cupom, c.tit_cupom, c.per_desc_cupom, co.nom_fantasia_comercio,
                   ca.dta_uso_cupom_associado
            FROM CUPOM_ASSOCIADO ca
            INNER JOIN CUPOM c ON ca.num_cupom = c.num_cupom
            INNER JOIN COMERCIO co ON c.cnpj_comercio = co.cnpj_comercio
            WHERE ca.cpf_associado = ? AND ca.dta_uso_cupom_associado IS NOT NULL
            ORDER BY ca.dta_uso_cupom_associado DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cpf]);
    $titulo = "Cupons Utilizados";
}

$cupons = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1><?php echo $titulo; ?></h1>
        <a href="dashboard_associado.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <div class="container">
        <?php if (count($cupons) == 0): ?>
            <p class="no-data">Nenhum cupom encontrado.</p>
        <?php else: ?>
            <div class="cupons-grid">
                <?php foreach ($cupons as $cupom): ?>
                    <div class="cupom-card">
                        <h3><?php echo htmlspecialchars($cupom['tit_cupom']); ?></h3>
                        <p class="comercio"><?php echo htmlspecialchars($cupom['nom_fantasia_comercio']); ?></p>
                        <?php if (isset($cupom['nom_categoria'])): ?>
                            <span class="categoria"><?php echo htmlspecialchars($cupom['nom_categoria']); ?></span>
                        <?php endif; ?>
                        <p class="desconto"><?php echo number_format($cupom['per_desc_cupom'], 2, ',', '.'); ?>% OFF</p>
                        
                        <?php if ($status === 'disponiveis'): ?>
                            <p class="validade">Válido até: <?php echo date('d/m/Y', strtotime($cupom['dta_termino_cupom'])); ?></p>
                            <form action="../actions/reservar_cupom_process.php" method="POST">
                                <input type="hidden" name="num_cupom" value="<?php echo $cupom['num_cupom']; ?>">
                                <button type="submit" class="btn btn-primary">Reservar</button>
                            </form>
                        <?php elseif ($status === 'reservados'): ?>
                            <p class="codigo">Código: <strong><?php echo $cupom['num_cupom']; ?></strong></p>
                            <p class="reservado">Reservado em: <?php echo date('d/m/Y', strtotime($cupom['dta_cupom_associado'])); ?></p>
                        <?php else: ?>
                            <p class="usado">Utilizado em: <?php echo date('d/m/Y', strtotime($cupom['dta_uso_cupom_associado'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comercio') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$cnpj = $_SESSION['user_id'];
$filtro = $_GET['filtro'] ?? 'todos';

// Query base
$sql = "SELECT c.num_cupom, c.tit_cupom, c.per_desc_cupom, c.dta_emissao_cupom,
               c.dta_inicio_cupom, c.dta_termino_cupom,
               COUNT(DISTINCT ca.id_cupom_associado) as total_reservas,
               COUNT(DISTINCT CASE WHEN ca.dta_uso_cupom_associado IS NOT NULL THEN ca.id_cupom_associado END) as total_usos
        FROM CUPOM c
        LEFT JOIN CUPOM_ASSOCIADO ca ON c.num_cupom = ca.num_cupom
        WHERE c.cnpj_comercio = ?";

// Adicionar filtros
if ($filtro === 'ativos') {
    $sql .= " AND c.dta_inicio_cupom <= CURDATE() AND c.dta_termino_cupom >= CURDATE()";
} elseif ($filtro === 'vencidos') {
    $sql .= " AND c.dta_termino_cupom < CURDATE()";
} elseif ($filtro === 'futuros') {
    $sql .= " AND c.dta_inicio_cupom > CURDATE()";
}

$sql .= " GROUP BY c.num_cupom ORDER BY c.dta_emissao_cupom DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$cnpj]);
$cupons = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Cupons</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Meus Cupons</h1>
        <a href="dashboard_comercio.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <div class="container">
        <div class="filters">
            <a href="?filtro=todos" class="btn <?php echo $filtro === 'todos' ? 'btn-primary' : 'btn-outline'; ?>">Todos</a>
            <a href="?filtro=ativos" class="btn <?php echo $filtro === 'ativos' ? 'btn-primary' : 'btn-outline'; ?>">Ativos</a>
            <a href="?filtro=futuros" class="btn <?php echo $filtro === 'futuros' ? 'btn-primary' : 'btn-outline'; ?>">Futuros</a>
            <a href="?filtro=vencidos" class="btn <?php echo $filtro === 'vencidos' ? 'btn-primary' : 'btn-outline'; ?>">Vencidos</a>
        </div>
        
        <?php if (count($cupons) == 0): ?>
            <p class="no-data">Nenhum cupom encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="cupons-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Desconto</th>
                            <th>Emissão</th>
                            <th>Início</th>
                            <th>Término</th>
                            <th>Reservas</th>
                            <th>Usos</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cupons as $cupom): 
                            $hoje = date('Y-m-d');
                            if ($cupom['dta_termino_cupom'] < $hoje) {
                                $status = 'Vencido';
                                $status_class = 'status-vencido';
                            } elseif ($cupom['dta_inicio_cupom'] > $hoje) {
                                $status = 'Futuro';
                                $status_class = 'status-futuro';
                            } else {
                                $status = 'Ativo';
                                $status_class = 'status-ativo';
                            }
                        ?>
                            <tr>
                                <td><code><?php echo $cupom['num_cupom']; ?></code></td>
                                <td><?php echo htmlspecialchars($cupom['tit_cupom']); ?></td>
                                <td><?php echo number_format($cupom['per_desc_cupom'], 2, ',', '.'); ?>%</td>
                                <td><?php echo date('d/m/Y', strtotime($cupom['dta_emissao_cupom'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cupom['dta_inicio_cupom'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cupom['dta_termino_cupom'])); ?></td>
                                <td><?php echo $cupom['total_reservas']; ?></td>
                                <td><?php echo $cupom['total_usos']; ?></td>
                                <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

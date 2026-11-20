<?php
require 'config.php';
$tot_clubes = $pdo->query("SELECT COUNT(*) FROM clubes")->fetchColumn();
$tot_miembros = $pdo->query("SELECT COUNT(*) FROM miembros")->fetchColumn();
$tot_insc = $pdo->query("SELECT COUNT(*) FROM inscripciones")->fetchColumn();
$resumen = $pdo->query("SELECT c.nombre, c.deporte, COUNT(i.inscripcion_id) AS total
                        FROM clubes c LEFT JOIN inscripciones i ON i.club_id = c.club_id
                        GROUP BY c.club_id ORDER BY total DESC")->fetchAll();
include 'includes/header.php';
?>
<h1>Sistema de Gestión de Clubes Deportivos</h1>
<p><strong>Clubes:</strong> <?= e($tot_clubes) ?> | <strong>Miembros:</strong> <?= e($tot_miembros) ?> | <strong>Inscripciones:</strong> <?= e($tot_insc) ?></p>
<h2>Miembros por club</h2>
<table>
  <tr><th>Club</th><th>Deporte</th><th>Miembros inscritos</th></tr>
  <?php foreach ($resumen as $r): ?>
    <tr><td><?= e($r['nombre']) ?></td><td><?= e($r['deporte']) ?></td><td><?= e($r['total']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php include 'includes/footer.php'; ?>

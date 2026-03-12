<?php
require_once '../config/database.php';
include '../includes/header.php';
include '../includes/navbar.php';

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Récupérer les infos du quiz
$stmtQuiz = $pdo->prepare("SELECT titre FROM quiz WHERE id = ?");
$stmtQuiz->execute([$quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) { header("Location: admin.php"); exit(); }

// Récupérer les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Questions pour : <span class="text-primary"><?= htmlspecialchars($quiz['titre']) ?></span></h4>
        <a href="admin.php" class="btn btn-outline-secondary btn-sm">Retour Dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th class="ps-4">Question</th>
                <th>Type</th>
                <th>Points</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($questions as $q): ?>
                <tr>
                    <td class="ps-4"><?= htmlspecialchars($q['question']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= $q['type'] ?></span></td>
                    <td><?= $q['points'] ?> pts</td>
                    <td class="text-end pe-4">
                        <a href="edit_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                        <a href="delete_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette question ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

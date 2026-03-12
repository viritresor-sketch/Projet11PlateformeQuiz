<?php
require_once '../config/database.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Récupération de tous les quiz
$stmt = $pdo->query("SELECT * FROM quiz ORDER BY id DESC");
$quizs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">Inventaire des Quiz</h1>
            <a href="admin.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour au Dashboard
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Chrono</th>
                        <th>Essais</th>
                        <th class="text-center">Ordre Aléatoire</th>
                        <th class="pe-4 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($quizs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun quiz enregistré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($quizs as $quiz): ?>
                            <tr class="align-middle">
                                <td class="ps-4 text-muted">#<?= $quiz['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($quiz['titre']) ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(substr($quiz['descriptions'], 0, 60)) ?>...
                                    </small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $quiz['temps_limite'] ?> min</span></td>
                                <td><?= $quiz['tentatives_max'] ?></td>
                                <td class="text-center">
                                    <?php if ($quiz['melanger_questions']): ?>
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Oui</span>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="bi bi-dash-circle"></i> Non</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="quiz.php?id=<?= $quiz['id'] ?>" class="btn btn-sm btn-primary">Tester</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
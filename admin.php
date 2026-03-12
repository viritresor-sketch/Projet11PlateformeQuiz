<?php
// 1. Connexion à la base de données
include ('../config/database.php');
include ('../includes/header.php');
include ('../includes/navbar.php');

try {
    // 2. Statistiques globales
    $countQuiz = $pdo->query("SELECT COUNT(*) FROM quiz")->fetchColumn();
    $countResultats = $pdo->query("SELECT COUNT(*) FROM resultats")->fetchColumn();

    // On calcule la moyenne des scores (si tu veux une moyenne en %, il faut adapter selon tes points max)
    $avgScore = $pdo->query("SELECT AVG(score) FROM resultats")->fetchColumn();
    $avgScore = $avgScore ? round($avgScore, 1) : 0;

    // 3. Liste des quiz
    $stmt = $pdo->query("SELECT q.*, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as nb_questions FROM quiz q ORDER BY id DESC");
    $allQuiz = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Logique d'affichage des stats détaillées
    $view_quiz_id = isset($_GET['stats_id']) ? intval($_GET['stats_id']) : null;
    $stats_questions = [];

    if ($view_quiz_id) {
        $stmt = $pdo->prepare("SELECT question, type, nb_tentatives, nb_reussites FROM questions WHERE quiz_id = ?");
        $stmt->execute([$view_quiz_id]);
        $stats_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-speedometer2"></i> Tableau de Bord</h2>
            <a href="add_quiz.php" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Nouveau Quiz
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary h4 mb-0">📑</div>
                        <div>
                            <h6 class="text-muted mb-0 small">Total Quiz</h6>
                            <h4 class="mb-0 fw-bold"><?php echo $countQuiz; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success h4 mb-0">👥</div>
                        <div>
                            <h6 class="text-muted mb-0 small">Participations</h6>
                            <h4 class="mb-0 fw-bold"><?php echo $countResultats; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning h4 mb-0">⭐</div>
                        <div>
                            <h6 class="text-muted mb-0 small">Moyenne Score</h6>
                            <h4 class="mb-0 fw-bold"><?php echo $avgScore; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Titre du Quiz</th>
                            <th>Questions</th>
                            <th>Configuration</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($allQuiz)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Aucun quiz créé.</td></tr>
                        <?php else: ?>
                            <?php foreach ($allQuiz as $quiz): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold"><?php echo htmlspecialchars($quiz['titre']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars(substr($quiz['descriptions'], 0, 45)); ?>...</small>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?php echo $quiz['nb_questions']; ?> questions</span></td>
                                    <td>
                                        <small class="d-block">🕒 <?php echo $quiz['temps_limite']; ?> min</small>
                                        <small class="d-block">🔄 <?php echo $quiz['tentatives_max']; ?> tentatives</small>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group shadow-sm border rounded">
                                            <a href="admin.php?stats_id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-white" title="Voir les stats">📊 Stats</a>
                                            <a href="liste_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-white">
                                                <i class="bi bi-list-check"></i> Questions
                                            </a>
                                            <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-white text-secondary"><i class="bi bi-pencil"></i></a>
                                            <a href="delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-white text-danger" onclick="return confirm('Supprimer définitivement ?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($view_quiz_id && !empty($stats_questions)): ?>
            <hr class="my-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Analyse des questions - Quiz #<?php echo $view_quiz_id; ?></h4>
                <a href="admin.php" class="btn btn-sm btn-outline-secondary">Masquer les stats</a>
            </div>
            <div class="row">
                <?php foreach ($stats_questions as $s):
                    $taux = ($s['nb_tentatives'] > 0) ? round(($s['nb_reussites'] / $s['nb_tentatives']) * 100) : 0;
                    $color = ($taux < 40) ? "bg-danger" : (($taux < 70) ? "bg-warning" : "bg-success");
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm p-3 h-100">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold text-truncate" style="max-width: 80%;"><?php echo htmlspecialchars($s['question']); ?></span>
                                <span class="badge <?php echo $color; ?>"><?php echo $taux; ?>%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $taux; ?>%"></div>
                            </div>
                            <div class="mt-2 small text-muted">
                                <?php echo $s['nb_reussites']; ?> réussites sur <?php echo $s['nb_tentatives']; ?> tentatives.
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php include ('../includes/footer.php'); ?>
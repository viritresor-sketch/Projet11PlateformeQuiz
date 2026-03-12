<?php
// 1. Connexion à la base de données
include ('config/database.php');
include ('includes/header.php');
include ('includes/navbar.php');

// Récupération des quiz avec le compte des questions
$stmt = $pdo->query("SELECT q.*, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as total_questions FROM quiz q");
$quizs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container mt-5">
        <div class="row mb-5 justify-content-center text-center">
            <div class="col-md-10">
                <h1 class="display-4 fw-bold">Prêt à tester vos connaissances ?</h1>
                <p class="lead text-muted">Choisissez un quiz parmi les thématiques ci-dessous et tentez d'atteindre le sommet du classement.</p>
                <hr class="w-25 mx-auto">
            </div>
        </div>

        <div class="row">
            <?php if (empty($quizs)): ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucun quiz n'est disponible pour le moment.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($quizs as $quiz): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($quiz['titre']); ?></h5>
                                    <span class="badge bg-primary rounded-pill">
                                    <?php echo $quiz['total_questions']; ?> Q.
                                </span>
                                </div>

                                <p class="card-text text-muted flex-grow-1">
                                    <?php
                                    // Correction ici : 'description' au lieu de 'descriptions'
                                    $desc = $quiz['descriptions'] ?? '';
                                    echo htmlspecialchars(substr($desc, 0, 100)) . (strlen($desc) > 100 ? '...' : '');
                                    ?>
                                </p>

                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-between text-small mb-3">
                                        <span><i class="bi bi-clock"></i> <?php echo $quiz['temps_limite']; ?> min</span>
                                        <span><i class="bi bi-person-badge"></i> <?php echo $quiz['tentatives_max']; ?> essai(s)</span>
                                    </div>

                                    <div class="d-grid">
                                        <a href="pages/jouer.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary fw-bold">
                                            Participer au Quiz
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<?php include ('includes/footer.php'); ?>
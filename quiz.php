<?php
require_once '../config/database.php';

// 1. Récupération et sécurisation des paramètres URL (venant de jouer.php)
$quiz_id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
$email    = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : "";
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : "Anonyme";

// 2. Vérification de l'existence du quiz
// Utilisation de 'descriptions' avec un 's' comme dans ta base de données
$stmtQuiz = $pdo->prepare("SELECT * FROM quiz WHERE id = ?");
$stmtQuiz->execute([$quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    // Si le quiz n'existe pas, retour à l'accueil
    header("Location: ../index.php");
    exit();
}

// 3. Récupération des Questions
// On gère le mélange aléatoire si l'option est activée en base
$order = ($quiz['melanger_questions'] == 1) ? "RAND()" : "id ASC";
$sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY $order";
$stmtQuestions = $pdo->prepare($sql);
$stmtQuestions->execute([$quiz_id]);
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body text-center">
                        <h4 class="card-title text-primary"><?php echo htmlspecialchars($quiz['titre']); ?></h4>
                        <p class="text-muted small">Candidat : <strong><?php echo $username; ?></strong></p>
                        <hr>

                        <div id="quiz-timer" class="display-4 mb-2 fw-bold text-dark">00:00</div>
                        <p class="text-uppercase small fw-bold text-muted">Temps restant</p>

                        <div class="progress mb-3" style="height: 12px;">
                            <div id="progress-bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <form id="quizForm" action="resultat.php" method="POST">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                    <input type="hidden" name="username" value="<?php echo $username; ?>">
                    <input type="hidden" name="email" value="<?php echo $email; ?>">
                    <input type="hidden" name="start_time" value="<?php echo time(); ?>">

                    <?php if (empty($questions)): ?>
                        <div class="alert alert-warning">Ce quiz ne contient aucune question pour le moment.</div>
                    <?php else: ?>
                        <?php foreach ($questions as $index => $q): ?>
                            <div class="card mb-4 shadow-sm border-0 question-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-secondary">Question <?php echo $index + 1; ?></span>
                                        <span class="text-muted small"><?php echo $q['points']; ?> point(s)</span>
                                    </div>

                                    <p class="lead fw-bold"><?php echo htmlspecialchars($q['question']); ?></p>
                                    <hr>

                                    <div class="options-container">
                                        <?php
                                        // Récupération des réponses pour cette question
                                        $stmtR = $pdo->prepare("SELECT * FROM reponses WHERE question_id = ?");
                                        $stmtR->execute([$q['id']]);
                                        $reponses = $stmtR->fetchAll();

                                        if ($q['type'] === 'reponse_courte'): ?>
                                            <div class="mb-3">
                                                <input type="text" name="reponse[<?php echo $q['id']; ?>]"
                                                       class="form-control form-control-lg"
                                                       placeholder="Saisissez votre réponse ici..." required>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($reponses as $r): ?>
                                                <div class="form-check p-3 border rounded mb-2 shadow-none transition-all">
                                                    <input class="form-check-input ms-1" type="radio"
                                                           name="reponse[<?php echo $q['id']; ?>]"
                                                           id="rep<?php echo $r['id']; ?>"
                                                           value="<?php echo $r['id']; ?>" required>
                                                    <label class="form-check-label ms-3 w-100" for="rep<?php echo $r['id']; ?>">
                                                        <?php echo htmlspecialchars($r['reponse']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2 mt-4 mb-5">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                                Soumettre mes réponses <i class="bi bi-check-circle-fill ms-2"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        const TIME_LIMIT = <?php echo intval($quiz['temps_limite']) * 60; ?>;
    </script>
    <script src="../js/timer.js"></script>

<?php include '../includes/footer.php'; ?>
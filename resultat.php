<?php
require_once '../config/database.php';

// 1. Récupération des données envoyées par quiz.php
$quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$nom_participant = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : "Anonyme";
$email_participant = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "";
$reponses_utilisateur = isset($_POST['reponse']) ? $_POST['reponse'] : [];
$start_time = isset($_POST['start_time']) ? intval($_POST['start_time']) : time();
$temps_ecoule = time() - $start_time; // Calcul de la durée réelle

// 2. Récupération des infos du quiz et des questions pour la correction
$stmtQuiz = $pdo->prepare("SELECT * FROM quiz WHERE id = ?");
$stmtQuiz->execute([$quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    header("Location: ../index.php");
    exit();
}

$stmtQ = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmtQ->execute([$quiz_id]);
$questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

$score_total = 0;
$points_max = 0;

include '../includes/header.php';
include '../includes/navbar.php';
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <h2>Résultats de <?php echo $nom_participant; ?></h2>
                        <p class="mb-0">Quiz : <?php echo htmlspecialchars($quiz['titre']); ?></p>
                    </div>
                    <div class="card-body p-5">

                        <?php
                        // 3. ANALYSE DES RÉPONSES
                        foreach ($questions as $q) {
                            $points_max += $q['points'];
                            $question_id = $q['id'];
                            $reussite_question = false;

                            // On cherche la bonne réponse en base
                            $stmtR = $pdo->prepare("SELECT * FROM reponses WHERE question_id = ? AND est_correcte = 1");
                            $stmtR->execute([$question_id]);
                            $bonne_reponse = $stmtR->fetch();

                            if (isset($reponses_utilisateur[$question_id])) {
                                $rep_u = $reponses_utilisateur[$question_id];

                                if ($q['type'] === 'reponse_courte') {
                                    // Comparaison insensible à la casse
                                    if (strtolower(trim($rep_u)) === strtolower(trim($bonne_reponse['reponse']))) {
                                        $reussite_question = true;
                                    }
                                } else {
                                    // Comparaison d'ID pour les QCM
                                    if (intval($rep_u) === intval($bonne_reponse['id'])) {
                                        $reussite_question = true;
                                    }
                                }
                            }

                            if ($reussite_question) {
                                $score_total += $q['points'];
                            }

                            // 4. MISE À JOUR DES STATS (nb_tentatives et nb_reussites)
                            $updateStats = $pdo->prepare("UPDATE questions SET nb_tentatives = nb_tentatives + 1, nb_reussites = nb_reussites + ? WHERE id = ?");
                            $updateStats->execute([$reussite_question ? 1 : 0, $question_id]);
                        }

                        // Calcul du pourcentage
                        $pourcentage = ($points_max > 0) ? round(($score_total / $points_max) * 100) : 0;
                        ?>

                        <div class="text-center mb-4">
                            <div class="display-3 fw-bold <?php echo ($pourcentage >= 70) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $score_total; ?> / <?php echo $points_max; ?>
                            </div>
                            <p class="lead">Soit une réussite de <strong><?php echo $pourcentage; ?>%</strong></p>
                        </div>

                        <div class="alert <?php echo ($pourcentage >= 70) ? 'alert-success' : 'alert-warning'; ?> text-center">
                            <?php if ($pourcentage >= 70): ?>
                                <i class="bi bi-trophy-fill fs-1 d-block mb-2"></i>
                                <strong>Félicitations !</strong> Vous avez validé ce quiz avec succès.
                            <?php else: ?>
                                <i class="bi bi-exclamation-triangle-fill fs-1 d-block mb-2"></i>
                                <strong>Dommage...</strong> Vous n'avez pas atteint les 70% requis pour le certificat.
                            <?php endif; ?>
                        </div>

                        <?php if ($pourcentage >= 70): ?>
                            <div class="d-grid gap-2 mt-4">
                                <form action="generer_certificat.php" method="POST">
                                    <input type="hidden" name="nom" value="<?php echo $nom_participant; ?>">
                                    <input type="hidden" name="quiz_titre" value="<?php echo htmlspecialchars($quiz['titre']); ?>">
                                    <input type="hidden" name="score" value="<?php echo $score_total . '/' . $points_max; ?>">
                                    <button type="submit" class="btn btn-outline-dark btn-lg w-100">
                                        <i class="bi bi-file-earmark-pdf"></i> Télécharger mon Certificat (PDF)
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php
                        $stmtSave = $pdo->prepare("INSERT INTO resultats (quiz_id, nom_participant, email, score, temps_ecoule, date_passage) VALUES (?, ?, ?, ?, ?, NOW())");
                        $stmtSave->execute([$quiz_id, $nom_participant, $email_participant, $score_total, $temps_ecoule]);
                        ?>

                        <div class="text-center mt-5">
                            <a href="../index.php" class="btn btn-secondary">Retour à l'accueil</a>
                            <a href="topScore.php" class="btn btn-link">Voir le classement</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
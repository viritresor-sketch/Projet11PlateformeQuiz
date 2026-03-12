<?php
require_once '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Récupérer la question et ses réponses
$stmtQ = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmtQ->execute([$id]);
$question = $stmtQ->fetch();

if (!$question) { die("Question introuvable."); }

$stmtR = $pdo->prepare("SELECT * FROM reponses WHERE question_id = ?");
$stmtR->execute([$id]);
$reponses = $stmtR->fetchAll();

// 2. Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texte_q = htmlspecialchars($_POST['question']);
    $points = intval($_POST['points']);

    // Mise à jour de la question
    $updateQ = $pdo->prepare("UPDATE questions SET question = ?, points = ? WHERE id = ?");
    $updateQ->execute([$texte_q, $points, $id]);

    // Mise à jour des réponses
    if (isset($_POST['reponses'])) {
        foreach ($_POST['reponses'] as $reponse_id => $texte_r) {
            $est_correcte = (isset($_POST['correcte']) && $_POST['correcte'] == $reponse_id) ? 1 : 0;

            $updateR = $pdo->prepare("UPDATE reponses SET reponse = ?, est_correcte = ? WHERE id = ?");
            $updateR->execute([htmlspecialchars($texte_r), $est_correcte, $reponse_id]);
        }
    }

    header("Location: liste_questions.php?quiz_id=" . $question['quiz_id']);
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="" method="POST" class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Modifier la Question</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Intitulé de la question</label>
                        <textarea name="question" class="form-control" rows="2" required><?= htmlspecialchars($question['question']) ?></textarea>
                    </div>

                    <div class="mb-4" style="max-width: 150px;">
                        <label class="form-label fw-bold">Points</label>
                        <input type="number" name="points" class="form-control" value="<?= $question['points'] ?>" required>
                    </div>

                    <h6 class="fw-bold mb-3 text-secondary">Options de réponses (cochez la bonne)</h6>
                    <?php foreach ($reponses as $r): ?>
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correcte" value="<?= $r['id'] ?>" <?= $r['est_correcte'] ? 'checked' : '' ?> required>
                            </div>
                            <input type="text" name="reponses[<?= $r['id'] ?>]" class="form-control" value="<?= htmlspecialchars($r['reponse']) ?>" required>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="liste_questions.php?quiz_id=<?= $question['quiz_id'] ?>" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">Enregistrer les changements</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

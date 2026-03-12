<?php
global $pdo;
require_once ('../config/database.php');
include ('../includes/header.php');
include ('../includes/navbar.php');
// On récupère l'ID du quiz depuis l'URL
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Vérifier si le quiz existe

$stmtQuiz = $pdo->prepare("SELECT titre FROM quiz WHERE id = ?");
$stmtQuiz->execute([$quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    header("Location: admin.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction(); // Utilisation d'une transaction pour garantir l'intégrité

        // 1. Insertion de la question
        $question_text = htmlspecialchars($_POST['question']);
        $type = $_POST['type'];
        $points = intval($_POST['points']);

        $sqlQ = "INSERT INTO questions (quiz_id, question, type, points) VALUES (?, ?, ?, ?)";
        $stmtQ = $pdo->prepare($sqlQ);
        $stmtQ->execute([$quiz_id, $question_text, $type, $points]);
        $question_id = $pdo->lastInsertId();

        // 2. Insertion des réponses selon le type
        if ($type === 'qcm') {
            foreach ($_POST['reponses'] as $index => $reponse_text) {
                $is_correct = ($index == $_POST['correct_qcm']) ? 1 : 0;
                $stmtR = $pdo->prepare("INSERT INTO reponses (question_id, reponse, est_correcte) VALUES (?, ?, ?)");
                $stmtR->execute([$question_id, htmlspecialchars($reponse_text), $is_correct]);
            }
        } elseif ($type === 'vrai_faux') {
            $reponses_vf = ['Vrai', 'Faux'];
            foreach ($reponses_vf as $val) {
                $is_correct = ($val === $_POST['correct_vf']) ? 1 : 0;
                $stmtR = $pdo->prepare("INSERT INTO reponses (question_id, reponse, est_correcte) VALUES (?, ?, ?)");
                $stmtR->execute([$question_id, $val, $is_correct]);
            }
        } elseif ($type === 'reponse_courte') {
            $stmtR = $pdo->prepare("INSERT INTO reponses (question_id, reponse, est_correcte) VALUES (?, ?, ?)");
            $stmtR->execute([$question_id, htmlspecialchars($_POST['correct_courte']), 1]);
        }

        $pdo->commit();
        $message = "<div class='alert alert-success'>Question ajoutée avec succès !</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}
?>


    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin.php">Admin</a></li>
                        <li class="breadcrumb-item active">Ajouter des questions à : <?php echo htmlspecialchars($quiz['titre']); ?></li>
                    </ol>
                </nav>

                <?php echo $message; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Nouvelle Question</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" id="questionForm">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">Énoncé de la question</label>
                                    <label>
                                        <input type="text" name="question" class="form-control" required>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Points</label>
                                    <label>
                                        <input type="number" name="points" class="form-control" value="1">
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Type</label>
                                    <label for="typeSelect"></label><select name="type" id="typeSelect" class="form-select">
                                        <option value="qcm">QCM</option>
                                        <option value="vrai_faux">Vrai/Faux</option>
                                        <option value="reponse_courte">Courte</option>
                                    </select>
                                </div>
                            </div>

                            <div id="reponsesContainer" class="p-3 border rounded bg-light mb-3">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-outline-secondary">Terminer</a>
                                <button type="submit" class="btn btn-success">Ajouter cette question</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script simple pour changer les champs de réponse selon le type choisi
        const container = document.getElementById('reponsesContainer');
        const typeSelect = document.getElementById('typeSelect');
        typeSelect.value = undefined;

        function updateFields() {
            const type = typeSelect.value;
            let html = '';

            if (type === 'qcm') {
                html = '<h6>Options (Cochez la bonne réponse) :</h6>';
                for (let i = 0; i < 4; i++) {
                    html += `
                <div class="input-group mb-2">
                    <div class="input-group-text">
                        <input type="radio" name="correct_qcm" value="${i}" ${i===0 ? 'checked' : ''}>
                    </div>
                    <input type="text" name="reponses[]" class="form-control" placeholder="Option ${i+1}" required>
                </div>`;
                }
            } else if (type === 'vrai_faux') {
                html = `
            <h6>Réponse correcte :</h6>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_vf" value="Vrai" checked>
                <label class="form-check-label">Vrai</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_vf" value="Faux">
                <label class="form-check-label">Faux</label>
            </div>`;
            } else if (type === 'reponse_courte') {
                html = `
            <label class="form-label">Réponse attendue :</label>
            <input type="text" name="correct_courte" class="form-control" required placeholder="La réponse exacte">`;
            }
            container.innerHTML = html;
        }

        typeSelect.addEventListener('change', updateFields);
        window.onload = updateFields;
    </script>

<?php include ('../includes/footer.php'); ?>
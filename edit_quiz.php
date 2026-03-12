<?php
require_once '../config/database.php';

// 1. Récupération du quiz à modifier
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM quiz WHERE id = ?");
$stmt->execute([$id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Quiz introuvable.");
}

// 2. Traitement de la mise à jour (quand on clique sur Enregistrer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $descriptions = htmlspecialchars($_POST['descriptions']); // On garde le 's'
    $temps = intval($_POST['temps_limite']);
    $tentatives = intval($_POST['tentatives_max']);
    $melanger = isset($_POST['melanger']) ? 1 : 0;

    $sql = "UPDATE quiz SET titre = ?, descriptions = ?, temps_limite = ?, tentatives_max = ?, melanger_questions = ? WHERE id = ?";
    $stmtUpdate = $pdo->prepare($sql);

    if ($stmtUpdate->execute([$titre, $descriptions, $temps, $tentatives, $melanger, $id])) {
        header("Location: admin.php?msg=modifie");
        exit();
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">Modifier le Quiz #<?php echo $id; ?></h3>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titre du Quiz</label>
                            <input type="text" name="titre" class="form-control" value="<?php echo htmlspecialchars($quiz['titre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description (utilisée sur l'accueil)</label>
                            <textarea name="descriptions" class="form-control" rows="3" required><?php echo htmlspecialchars($quiz['descriptions']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Temps Limite (minutes)</label>
                                <input type="number" name="temps_limite" class="form-control" value="<?php echo $quiz['temps_limite']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tentatives Max</label>
                                <input type="number" name="tentatives_max" class="form-control" value="<?php echo $quiz['tentatives_max']; ?>" required>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="melanger" id="melanger" <?php echo $quiz['melanger_questions'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="melanger">Mélanger l'ordre des questions</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="admin.php" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success px-4">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

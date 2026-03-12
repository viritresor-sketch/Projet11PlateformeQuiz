<?php
require_once '../config/database.php';
$message = "";
// 2. Traitement du formulaire lors de la soumission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et sécurisation des données
    $titre = htmlspecialchars($_POST['titre']);
    $descriptions = htmlspecialchars($_POST['descriptions']);
    $temps_limite = intval($_POST['temps_limite']);
    $tentatives_max = intval($_POST['tentatives_max']);

    $melanger = isset($_POST['melanger']) ? 1 : 0;

    try {
        // Préparation de la requête SQL d'insertion [cite: 6, 8, 9, 10, 11, 12, 13]
        $sql = "INSERT INTO quiz (titre, descriptions, temps_limite, tentatives_max, melanger_questions) 
                VALUES (:titre, :descriptions, :temps_limite, :tentatives_max, :melanger)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
                ':titre' => $titre,
                ':descriptions' => $descriptions,
                ':temps_limite' => $temps_limite,
                ':tentatives_max' => $tentatives_max,
                ':melanger' => $melanger
        ]);

        // Redirection vers l'ajout de questions avec l'ID du quiz créé
        $new_quiz_id = $pdo->lastInsertId();
        header("Location: add_question.php?quiz_id=" . $new_quiz_id);
        exit();

    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur lors de l'ajout : " . $e->getMessage() . "</div>";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin.php">Admin</a></li>
                        <li class="breadcrumb-item active">Créer un Quiz</li>
                    </ol>
                </nav>

                <?php echo $message; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Nouveau Quiz</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Titre du Quiz</label>
                                <input type="text" name="titre" class="form-control" required placeholder="Ex: Réseaux de neurones">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="descriptions" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Temps limite (minutes)</label>
                                    <input type="number" name="temps_limite" class="form-control" value="15" min="1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tentatives maximum</label>
                                    <input type="number" name="tentatives_max" class="form-control" value="1" min="1">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="melanger" id="shuffle" checked>
                                    <label class="form-check-label" for="shuffle">Mélanger les questions pour chaque candidat</label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Étape suivante : Ajouter les questions
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
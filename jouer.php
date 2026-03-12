<?php
require_once '../config/database.php';

// 1. Récupération de l'ID du quiz
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Vérification et récupération des infos (on garde 'descriptions' comme convenu)
$stmt = $pdo->prepare("SELECT titre, descriptions, temps_limite FROM quiz WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php';
// On peut choisir de ne pas inclure la navbar ici pour une immersion totale
?>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .play-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }
        .play-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-custom {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #eee;
        }
        .input-custom:focus {
            border-color: #667eea;
            box-shadow: none;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card play-card shadow-lg">
                    <div class="card-body p-0">
                        <div class="p-4 text-center bg-white">
                            <div class="display-6 mb-2">🚀</div>
                            <h2 class="fw-bold mb-1">Prêt pour le défi ?</h2>
                            <p class="text-muted small"><?php echo htmlspecialchars($quiz['titre']); ?></p>
                        </div>

                        <div class="p-4 bg-light">
                            <form action="quiz.php" method="GET">
                                <input type="hidden" name="id" value="<?php echo $quiz_id; ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase">Nom Complet</label>
                                    <input type="text" name="username" class="form-control input-custom"
                                           placeholder="Votre pseudo ou nom réel" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase">Adresse Email</label>
                                    <input type="email" name="email" class="form-control input-custom"
                                           placeholder="nom@exemple.com" required>
                                    <div class="form-text mt-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-info-circle"></i> Utilisé pour l'envoi de votre certificat.
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow"
                                            style="border-radius: 12px; background: #667eea; border: none;">
                                        Lancer le Quiz (<?php echo $quiz['temps_limite']; ?> min)
                                    </button>
                                    <a href="../index.php" class="btn btn-link btn-sm text-muted">Annuler et revenir</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50">
                    <small>
                        <i class="bi bi-shield-lock"></i> Vos données sont utilisées uniquement pour ce quiz.
                    </small>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
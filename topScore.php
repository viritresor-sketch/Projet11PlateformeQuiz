<?php
require_once '../config/database.php';

try {
    // Requête pour récupérer le Top 10 global ou par quiz
    $quiz_filter = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : null;

    $sql = "SELECT r.*, q.titre as quiz_titre 
            FROM resultats r 
            JOIN quiz q ON r.quiz_id = q.id";

    if ($quiz_filter) {
        $sql .= " WHERE r.quiz_id = :quiz_id";
    }

    $sql .= " ORDER BY r.score DESC, r.temps_ecoule ASC LIMIT 10";

    $stmt = $pdo->prepare($sql);
    if ($quiz_filter) {
        $stmt->execute([':quiz_id' => $quiz_filter]);
    } else {
        $stmt->execute();
    }
    $top_scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer la liste des quiz pour le filtre
    $quiz_list = $pdo->query("SELECT id, titre FROM quiz")->fetchAll();

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

    <div class="container mt-4">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">🏆 Tableau des Scores</h1>
            <p class="lead">Les 10 meilleures performances de la plateforme</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <form method="GET" class="input-group">
                    <select name="quiz_id" class="form-select">
                        <option value="">Tous les Quiz</option>
                        <?php foreach ($quiz_list as $qz): ?>
                            <option value="<?php echo $qz['id']; ?>" <?php echo $quiz_filter == $qz['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($qz['titre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                </form>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Rang</th>
                                <th>Participant</th>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Temps</th>
                                <th class="pe-4">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($top_scores)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Aucun score enregistré pour le moment.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($top_scores as $index => $score): ?>
                                    <tr class="<?php echo $index === 0 ? 'table-warning' : ''; ?>">
                                        <td class="ps-4 fw-bold">
                                            <?php
                                            if ($index === 0) echo "🥇 1er";
                                            elseif ($index === 1) echo "🥈 2ème";
                                            elseif ($index === 2) echo "🥉 3ème";
                                            else echo ($index + 1) . "ème";
                                            ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($score['nom_participant']); ?></strong></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($score['quiz_titre']); ?></span></td>
                                        <td class="fw-bold text-primary"><?php echo $score['score']; ?> pts</td>
                                        <td><?php echo gmdate("i:s", $score['temps_ecoule']); ?></td>
                                        <td class="text-muted pe-4"><?php echo date('d/m/Y', strtotime($score['date_passage'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="../index.php" class="btn btn-outline-primary">Relever un défi</a>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
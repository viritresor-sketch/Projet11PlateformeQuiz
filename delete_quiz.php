<?php
require_once '../config/database.php';

// 1. Vérifier si l'ID est présent
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Début d'une transaction pour tout supprimer proprement
        $pdo->beginTransaction();

        // 2. Supprimer les réponses liées aux questions de ce quiz
        $stmt1 = $pdo->prepare("DELETE FROM reponses WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
        $stmt1->execute([$id]);

        // 3. Supprimer les questions liées au quiz
        $stmt2 = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt2->execute([$id]);

        // 4. Supprimer les résultats liés au quiz
        $stmt3 = $pdo->prepare("DELETE FROM resultats WHERE quiz_id = ?");
        $stmt3->execute([$id]);

        // 5. Enfin, supprimer le quiz
        $stmt4 = $pdo->prepare("DELETE FROM quiz WHERE id = ?");
        $stmt4->execute([$id]);

        $pdo->commit();

        // Redirection vers l'admin avec un message de succès
        header("Location: admin.php?msg=supprime");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: admin.php");
    exit();
}
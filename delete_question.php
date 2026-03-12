<?php
require_once '../config/database.php';

// 1. Vérification de l'existence de l'ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Début d'une transaction pour garantir que tout est supprimé ensemble
        $pdo->beginTransaction();

        // On récupère d'abord l'ID du quiz pour la redirection finale
        $stmtGetQuiz = $pdo->prepare("SELECT quiz_id FROM questions WHERE id = ?");
        $stmtGetQuiz->execute([$id]);
        $question = $stmtGetQuiz->fetch();

        if ($question) {
            $quiz_id = $question['quiz_id'];

            // 2. Supprimer les réponses liées à cette question
            $stmt1 = $pdo->prepare("DELETE FROM reponses WHERE question_id = ?");
            $stmt1->execute([$id]);

            // 3. Supprimer la question
            $stmt2 = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt2->execute([$id]);

            $pdo->commit();

            // Redirection vers la liste des questions du quiz avec un message
            header("Location: liste_questions.php?quiz_id=" . $quiz_id . "&msg=deleted");
            exit();
        } else {
            header("Location: admin.php");
            exit();
        }

    } catch (PDOException $e) {
        // En cas d'erreur, on annule tout
        $pdo->rollBack();
        die("Erreur lors de la suppression de la question : " . $e->getMessage());
    }
} else {
    header("Location: admin.php");
    exit();
}

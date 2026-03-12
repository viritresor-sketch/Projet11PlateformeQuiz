<?php
// Fonction pour sauvegarder le résultat et mettre à jour les statistiques
function saveQuizPerformance($pdo, $quiz_id, $participant_name, $score, $temps, $details_reponses) {
    try {
        $pdo->beginTransaction();

        // 1. Sauvegarde du résultat global du participant
        $sqlResultat = "INSERT INTO resultats (quiz_id, nom_participant, score, temps_ecoule) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlResultat);
        $stmt->execute([$quiz_id, $participant_name, $score, $temps]);
        $resultat_id = $pdo->lastInsertId();

        // 2. Sauvegarde du détail de chaque réponse et mise à jour des stats questions
        // On suppose que $details_reponses est un tableau : [question_id => ['correct' => true/false, 'valeur' => '...']]
        foreach ($details_reponses as $q_id => $data) {

            /* OPTIONNEL : Si tu as une table 'reponses_utilisateurs' pour garder un historique
               très précis de ce que chaque personne a répondu.
            */
            // $sqlHist = "INSERT INTO historique_reponses (resultat_id, question_id, reponse_donnee, est_correcte) VALUES (?, ?, ?, ?)";
            // ...

            // 3. MISE À JOUR DES STATS GLOBALES (C'est ici que ça devient intéressant)
            // On incrémente le nombre de fois où la question a été posée et le nombre de réussites
            $sqlStats = "UPDATE questions 
                         SET nb_tentatives = nb_tentatives + 1, 
                             nb_reussites = nb_reussites + (CASE WHEN ? = 1 THEN 1 ELSE 0 END)
                         WHERE id = ?";
            $stmtStats = $pdo->prepare($sqlStats);
            $stmtStats->execute([$data['correct'] ? 1 : 0, $q_id]);
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        return false;
    }
}
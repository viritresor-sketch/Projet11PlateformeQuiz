<?php
require_once '../config/database.php';
require_once '../libs/fpdf.php'; // Vérifie bien ce chemin !

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = strtoupper(htmlspecialchars($_POST['nom']));
    $quiz_titre = htmlspecialchars($_POST['quiz_titre']);
    $score = htmlspecialchars($_POST['score']);
    $date = date('d/m/Y');

    // 1. Initialisation du PDF en mode Paysage (L), unité mm, format A4
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);

    // 2. Création d'une bordure décorative
    $pdf->SetLineWidth(1.5);
    $pdf->Rect(10, 10, 277, 190); // Bordure extérieure
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(13, 13, 271, 184); // Bordure intérieure

    // 3. Titre Principal
    $pdf->SetFont('Arial', 'B', 35);
    $pdf->Ln(30);
    $pdf->Cell(0, 15, 'CERTIFICAT DE REUSSITE', 0, 1, 'C');

    // Petite ligne de séparation
    $pdf->SetDrawColor(102, 126, 234); // Couleur bleue
    $pdf->Line(100, 55, 197, 55);
    $pdf->Ln(15);

    // 4. Texte de présentation
    $pdf->SetFont('Arial', '', 18);
    $pdf->Cell(0, 10, 'Ce document est fiereinement decerne a :', 0, 1, 'C');
    $pdf->Ln(10);

    // 5. Nom du participant
    $pdf->SetFont('Times', 'BI', 40);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell(0, 20, utf8_decode($nom), 0, 1, 'C');
    $pdf->Ln(10);

    // 6. Détails du Quiz
    $pdf->SetTextColor(0, 0, 0); // Retour au noir
    $pdf->SetFont('Arial', '', 16);
    $pdf->MultiCell(0, 10, utf8_decode("Pour avoir complete avec succes le quiz :\n" . '"' . $quiz_titre . '"'), 0, 'C');
    $pdf->Ln(10);

    // 7. Score final
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, 'SCORE FINAL : ' . $score, 0, 1, 'C');

    // 8. Bas de page (Date et Signature fictive)
    $pdf->SetY(-50);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Fait le ' . $date, 0, 0, 'L');

    $pdf->SetX(-70);
    $pdf->SetFont('Courier', 'B', 15);
    $pdf->Cell(0, 10, 'L\'equipe PlateformeQuiz', 0, 0, 'R');

    // 9. Sortie du PDF (D pour Forcer le téléchargement)
    $pdf->Output('D', 'Certificat_' . $nom . '.pdf');
} else {
    header("Location: ../index.php");
    exit();
}
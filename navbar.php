<?php
// On s'assure que la base_url est bien définie, sinon on met une valeur par défaut
$base_url = isset($base_url) ? $base_url : '/PlateformeQuiz';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $base_url ?>/index.php">
            <i class="bi bi-patch-check-fill"></i> Plateforme de Quiz
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto gap-2">
                <a href="<?= $base_url ?>/index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-house-door"></i> Accueil
                </a>
                <a href="<?= $base_url ?>/pages/topScore.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-trophy"></i> Classement
                </a>

                <div class="vr mx-2 text-white opacity-25 d-none d-lg-block"></div>

                <a href="<?= $base_url ?>/pages/admin.php" class="btn btn-warning btn-sm fw-bold">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="dropdown">
                    <button class="btn btn-warning btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                        Gestion
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/add_quiz.php">Ajouter un Quiz</a></li>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/liste_quiz.php">Liste technique</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
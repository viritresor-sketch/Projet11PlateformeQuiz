let timeRemaining = TIME_LIMIT;
const timerDisplay = document.getElementById('quiz-timer');
const progressBar = document.getElementById('progress-bar');
const quizForm = document.getElementById('quizForm');

const countdown = setInterval(() => {
    let minutes = Math.floor(timeRemaining / 60);
    let seconds = timeRemaining % 60;

    // Formatage 00:00
    timerDisplay.innerHTML =
        (minutes < 10 ? "0" : "") + minutes + ":" +
        (seconds < 10 ? "0" : "") + seconds;

    // Mise à jour de la barre de progression
    let percentage = ((TIME_LIMIT - timeRemaining) / TIME_LIMIT) * 100;
    progressBar.style.width = percentage + "%";

    if (timeRemaining <= 0) {
        clearInterval(countdown);
        alert("Temps écoulé ! Vos réponses vont être envoyées.");
        quizForm.submit(); // Envoi automatique
    }

    if (timeRemaining <= 30) {
        timerDisplay.classList.add('text-danger'); // Alerte visuelle
    }

    timeRemaining--;
}, 1000);
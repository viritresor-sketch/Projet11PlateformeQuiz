/**
 * quiz.js - Gestion de l'interactivité du questionnaire
 */

document.addEventListener('DOMContentLoaded', () => {
    const quizForm = document.getElementById('quizForm');
    const questionCards = document.querySelectorAll('.question-card');
    const totalQuestions = questionCards.length;

    // 1. Surlignage de la réponse sélectionnée (pour les QCM et Vrai/Faux)
    // Cela permet à l'utilisateur de bien voir ce qu'il a coché
    const radioInputs = document.querySelectorAll('input[type="radio"]');

    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            // On retire la classe active de toutes les options de cette question
            const parentCard = this.closest('.card-body');
            parentCard.querySelectorAll('.form-check').forEach(el => {
                el.classList.remove('bg-primary', 'bg-opacity-10', 'border-primary');
                el.style.borderRadius = "8px";
            });

            // On ajoute le style à l'option sélectionnée
            if (this.checked) {
                const selectionLabel = this.closest('.form-check');
                selectionLabel.classList.add('bg-primary', 'bg-opacity-10', 'border-primary');
                selectionLabel.style.border = "1px solid";
            }

            updateProgressBar();
        });
    });

    // 2. Mise à jour d'une barre de progression de complétion
    // Différente du timer, elle montre combien de questions ont été répondues
    function updateProgressBar() {
        const answeredQuestions = new Set();
        const inputs = quizForm.querySelectorAll('input[required]');

        inputs.forEach(input => {
            if (input.type === 'radio' && input.checked) {
                answeredQuestions.add(input.name);
            } else if (input.type === 'text' && input.value.trim() !== "") {
                answeredQuestions.add(input.name);
            }
        });

        const progressPercent = (answeredQuestions.size / totalQuestions) * 100;
        const progressBar = document.getElementById('progress-bar-completion');
        if (progressBar) {
            progressBar.style.width = `${progressPercent}%`;
        }
    }

    // 3. Validation avant soumission
    // Empêche d'envoyer le quiz par erreur s'il manque des réponses
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            const unanswered = [];

            questionCards.forEach((card, index) => {
                const radios = card.querySelectorAll('input[type="radio"]');
                const textInput = card.querySelector('input[type="text"]');

                let isAnswered = false;
                if (radios.length > 0) {
                    isAnswered = Array.from(radios).some(r => r.checked);
                } else if (textInput) {
                    isAnswered = textInput.value.trim() !== "";
                }

                if (!isAnswered) {
                    unanswered.push(index + 1);
                }
            });

            if (unanswered.length > 0) {
                const confirmSend = confirm(
                    `Il vous reste ${unanswered.length} question(s) sans réponse (Questions: ${unanswered.join(', ')}). \n\nVoulez-vous quand même envoyer vos résultats ?`
                );
                if (!confirmSend) {
                    e.preventDefault(); // Annule l'envoi
                }
            }
        });
    }

    // 4. Désactivation du clic droit et du copier-coller ( Anti-triche)

    document.addEventListener('contextmenu', event => event.preventDefault());
    quizForm.addEventListener('copy', e => e.preventDefault());
    quizForm.addEventListener('paste', e => e.preventDefault());

});
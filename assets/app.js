import './bootstrap.js';
import {
    Application
} from "@hotwired/stimulus";
import ToastController from "./controllers/toast_controller.js";
import AOS from "aos";
import FilterCourses from './js/modules/FilterCourses.js'
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/courses.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'aos/dist/aos.css';
import 'bootstrap';

window.Stimulus = Application.start();
Stimulus.register("toast", ToastController);

// Easy selector helper function
const select = (el, all = false) => {
    el = el.trim()
    if (all) {
        return [...document.querySelectorAll(el)]
    } else {
        return document.querySelector(el)
    }
}

// Easy on scroll event listener
const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
}

// Back to top button
const backToTop = () => {
    const backtotop = select('.back-to-top');
    if (backtotop) {
        const toggleBacktotop = () => {
            backtotop.classList.toggle('active', window.scrollY > 100);
        };
        window.addEventListener('load', toggleBacktotop);
        onscroll(document, toggleBacktotop);
    }
}

// Init Toggle page Section
const initToggle = () => {
    const btnToggle = document.getElementById("btnToggle");
    if (btnToggle) {
        btnToggle.addEventListener("click", () => {
            const sidebarElement = document.getElementById('customer-sidebar');
            const sidebarContainer = document.getElementById('fullContainer');
            const openFullscreen = document.getElementById('openFullscreen');
            const closeFullscreen = document.getElementById('closeFullscreen');

            sidebarElement.classList.toggle("d-none");
            sidebarContainer.classList.toggle('fullscreen', !sidebarContainer.classList.contains(
                'fullscreen'));
            sidebarContainer.classList.toggle('sidebar-container');
            openFullscreen.classList.toggle('d-none');
            closeFullscreen.classList.toggle('d-none');
        });
    }
}

// add active class on a page courses_show
const courseShow = () => {
    const courseContent = document.getElementById('show');
    if (courseContent) {
        const slug = window.location.pathname.split('/')[4];
        document.querySelectorAll("a[aria-label]").forEach(a => {
            if (a.getAttribute("aria-label") === slug) {
                a.classList.add("active");
            }
        });
    }
}

// Close alert message after 5 secondes
const closeAlertMessage = () => {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.classList.add('fade-out'); // Ajoutez une classe CSS pour transition
            setTimeout(() => alert.remove(), 1000); // Retire l'alerte après la transition
        }, 4000);
    }
}

const collapseButton = () => {
    const collapseIcons = document.querySelectorAll('.icon[data-bs-toggle="collapse"]');

    collapseIcons.forEach(icon => {
        icon.addEventListener("click", (e) => {
            const target = document.querySelector(icon.getAttribute("data-bs-target"));
            const closedIcon = icon.closest("header").querySelector(".icon.closed");
            const openedIcon = icon.closest("header").querySelector(".icon.opened");

            target.addEventListener("shown.bs.collapse", () => {
                openedIcon.classList.add("d-none");
                closedIcon.classList.remove("d-none");
            });

            target.addEventListener("hidden.bs.collapse", () => {
                openedIcon.classList.remove("d-none");
                closedIcon.classList.add("d-none");
            });
        });
    });
}

const animateOnScroll = () => {
    const elements = document.querySelectorAll('.fade-up');

    elements.forEach(el => {
        const rect = el.getBoundingClientRect();
        const inViewport = rect.top <= (window.innerHeight || document.documentElement.clientHeight);

        if (inViewport) {
            el.classList.add('visible');
        } else {
            el.classList.remove('visible');
        }
    });
};

const initAnimations = () => {
    // Applique l'animation sur tous les éléments au chargement
    animateOnScroll();

    // Réapplique l'animation lors du défilement
    document.addEventListener('scroll', animateOnScroll, {
        passive: true
    });
    window.addEventListener('resize', animateOnScroll, {
        passive: true
    });
};

// Fonction de soumission des réponses de quiz
const submitQuizAnswers = () => {
    const submitButton = document.getElementById('submit-quiz-button'); // Bouton "Soumettre la réponse"
    const nextButton = document.getElementById('next-question-button'); // Bouton "Prochaine question"
    const viewResultsButton = document.getElementById('view-results-button'); // Bouton "Voir les résultats"
    const sectionElement = document.getElementById('quiz-section-id');
    const sectionId = sectionElement ? sectionElement.dataset.sectionId : null;
    let currentQuestionIndex = 0;

    // Sélectionner toutes les questions
    const questions = document.querySelectorAll('.question');

    if (!submitButton && !nextButton && !viewResultsButton && !sectionElement) {
        return;
    }

    // Afficher la question à l'index donné
    function showQuestion(index) {
        questions.forEach((question, i) => {
            question.classList.toggle('d-none', i !== index); // Afficher uniquement la question active
        });

        // Réinitialiser les boutons
        submitButton.classList.add('disabled');
        submitButton.disabled = true;
        nextButton.classList.add('d-none');
        viewResultsButton.classList.add('d-none');

        // Si c'est la dernière question
        if (index === questions.length - 1) {
            // Afficher le bouton "Voir les résultats" et cacher le bouton "Soumettre"
            submitButton.classList.add('d-none');
        } else {
            // Afficher le bouton "Soumettre" et cacher "Voir les résultats"
            submitButton.classList.remove('d-none');
            viewResultsButton.classList.add('d-none');
        }
    }

    // Gérer la sélection des réponses
    function handleAnswerSelection(event) {
        const selectedAnswer = event.target;

        // Appliquer la classe 'selected' pour marquer visuellement la réponse choisie
        document.querySelectorAll(`input[name="${selectedAnswer.name}"]`).forEach(input => {
            input.parentElement.classList.remove('selected');
        });
        selectedAnswer.parentElement.classList.add('selected');

        // Activer le bouton "Soumettre"
        submitButton.classList.remove('disabled');
        submitButton.disabled = false;
    }

    function handleViewResults() {
        // Collecter les réponses de chaque question
        const answers = Array.from(document.querySelectorAll('.question')).map(question => {
            const questionId = question.dataset.questionId;
            const selectedAnswer = question.querySelector('input[type="radio"]:checked');
            return {
                questionId: questionId,
                answerId: selectedAnswer ? selectedAnswer.value : null
            };
        });
    
        // Envoi de toutes les réponses au serveur pour les enregistrer
        fetch("/quiz/finalize", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_csrf_token"]').value
            },
            body: JSON.stringify({ 
                sectionId: sectionId,
                answers: answers 
            })
        })
        .then(response => response.json())
        .then(data => {           
            if (data.redirectUrl) {
                window.location.href = data.redirectUrl; // Rediriger vers la page de résultats
            } else {
                console.error("Erreur lors de l'enregistrement des résultats:", data.error);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la finalisation du quiz:', error);
        });
    }

    // Soumettre la réponse et vérifier la validité
    function handleSubmit() {
        const currentQuestion = questions[currentQuestionIndex];
        const selectedAnswer = currentQuestion.querySelector('input[type="radio"]:checked');

        if (!selectedAnswer) {
            alert('Veuillez sélectionner une réponse.');
            return;
        }

        // Envoie de la réponse au serveur via AJAX
        fetch("/quiz/submit", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_csrf_token"]').value
                },
                body: JSON.stringify({
                    questionId: currentQuestion.dataset.questionId,
                    answerId: selectedAnswer.value
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse du serveur:', data);

                // Appliquer la classe 'correct' ou 'incorrect' selon la validité de la réponse
                const feedbackClass = data.correct ? 'correct' : 'incorrect';
                selectedAnswer.parentElement.classList.add(feedbackClass);

                // Afficher l'explication de la question
                const explanation = currentQuestion.querySelector('.explanation');
                if (explanation) {
                    explanation.classList.remove('d-none');
                }

                if (currentQuestionIndex < questions.length - 1) {
                    nextButton.classList.remove('d-none');
                    submitButton.classList.add('d-none');
                } else {
                    submitButton.classList.add('d-none');
                    viewResultsButton.classList.remove('d-none');
                    // setTimeout(() => {
                    //     submitButton.classList.add('d-none');
                    //     viewResultsButton.classList.remove('d-none');
                    // }, 500);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la soumission de la réponse:', error);
            });
    }

    // Gérer le clic sur le bouton "Prochaine question"
    function handleNextQuestion() {
        // Passer à la question suivante
        currentQuestionIndex++;

        if (currentQuestionIndex === 1) { // Lorsqu'on quitte la première question
            const navigationPartial = document.getElementById('navigation-partial');
            if (navigationPartial) {
                navigationPartial.classList.add('d-none'); // Masquer la navigation
            }
        }

        if (currentQuestionIndex < questions.length) {
            showQuestion(currentQuestionIndex);

            // Réinitialiser l'état des boutons pour la question suivante
            submitButton.classList.remove('d-none');
            nextButton.classList.add('d-none');
        } else {
            // Fin du quiz : afficher les résultats
            handleViewResults();
        }
    }

    // Attacher les écouteurs d'événements pour les réponses
    document.querySelectorAll('input[type="radio"]').forEach(input => {
        input.addEventListener('change', handleAnswerSelection);
    });

    // Attacher l'événement de soumission du quiz
    submitButton.addEventListener('click', handleSubmit);

    // Attacher l'événement de passage à la question suivante
    nextButton.addEventListener('click', handleNextQuestion);

    viewResultsButton.addEventListener('click', handleViewResults);

    // Afficher la première question
    showQuestion(currentQuestionIndex);
};

const showCorrectionText = () => {
    document.getElementById('correctionText').style.display = 'block';
};

const showCorrection = document.getElementById('showCorrection');

const initPage = () => {
    console.log("Initialisation de la page et d'AOS");
    closeAlertMessage();
    backToTop();
    initToggle();
    courseShow();
    collapseButton();
    initAnimations();
    submitQuizAnswers();
    new FilterCourses(document.querySelector('.js-filter-courses'));
    if (showCorrection) {
        showCorrection.addEventListener('click', showCorrectionText);
    }

    AOS.init({
        duration: 1200,
        once: true,
        disableMutationObserver: true,
        mirror: true
    });

    AOS.refresh();
};

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('load', initPage);
document.addEventListener('turbo:load', initPage);

// // Événements Turbo : exécution du script après les mises à jour du DOM par Turbo
// document.addEventListener('turbo:frame-load', function () {
//     closeAlertMessage();
//     backToTop();
//     initToggle();
// });

// document.addEventListener('turbo:render', function () {
//     closeAlertMessage();
//     backToTop();
//     initToggle();
// });

// document.addEventListener('turbo:before-render', function () {
//     closeAlertMessage();
//     backToTop();
//     initToggle();
// });
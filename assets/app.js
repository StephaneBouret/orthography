import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('DOMContentLoaded', function () {
    closeAlertMessage();
});

// Événements Turbo : exécution du script après les mises à jour du DOM par Turbo
document.addEventListener('turbo:load', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:frame-load', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:render', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:before-render', function () {
    closeAlertMessage();
});

const closeAlertMessage = () => {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(function () {
            alert.style.transition = "opacity 1s ease";
            alert.style.opacity = '0';

            setTimeout(function () {
                alert.style.display = 'none';
            }, 1000); // Après la transition d'opacité (1 seconde)
        }, 4000); // Après 5 secondes
    }
}
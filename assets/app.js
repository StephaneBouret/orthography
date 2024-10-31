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
    let backtotop = select('.back-to-top')
    if (backtotop) {
        const toggleBacktotop = () => {
            if (window.scrollY > 100) {
                backtotop.classList.add('active')
            } else {
                backtotop.classList.remove('active')
            }
        }
        window.addEventListener('load', toggleBacktotop)
        onscroll(document, toggleBacktotop)
    }
}

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('DOMContentLoaded', function () {
    closeAlertMessage();
    backToTop();
});

// Événements Turbo : exécution du script après les mises à jour du DOM par Turbo
document.addEventListener('turbo:load', function () {
    closeAlertMessage();
    backToTop();
});

document.addEventListener('turbo:frame-load', function () {
    closeAlertMessage();
    backToTop();
});

document.addEventListener('turbo:render', function () {
    closeAlertMessage();
    backToTop();
});

document.addEventListener('turbo:before-render', function () {
    closeAlertMessage();
    backToTop();
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
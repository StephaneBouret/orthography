import './bootstrap.js';
import {
    Application
} from "@hotwired/stimulus";
import ToastController from "./controllers/toast_controller.js";
import AOS from "aos";
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

const initPage = () => {
    console.log("Initialisation de la page et d'AOS");
    closeAlertMessage();
    backToTop();
    initToggle();
    courseShow();
    collapseButton();
    initAnimations();
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
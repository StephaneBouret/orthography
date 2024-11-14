<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'deleteAccount' => [
        'path' => './assets/js/deleteAccount.js',
        'entrypoint' => true,
    ],
    'register' => [
        'path' => './assets/js/register.js',
        'entrypoint' => true,
    ],
    'resetpw' => [
        'path' => './assets/js/resetpw.js',
        'entrypoint' => true,
    ],
    'updatepw' => [
        'path' => './assets/js/updatepw.js',
        'entrypoint' => true,
    ],
    'location' => [
        'path' => './assets/js/location.js',
        'entrypoint' => true,
    ],
    'admin_custom' => [
        'path' => './assets/js/admin_custom.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.css' => [
        'version' => '6.6.0',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'aos' => [
        'version' => '3.0.0-beta.6',
    ],
    'lodash.throttle' => [
        'version' => '4.1.1',
    ],
    'lodash.debounce' => [
        'version' => '4.0.8',
    ],
    'aos/dist/aos.css' => [
        'version' => '2.3.4',
        'type' => 'css',
    ],
    'flip-toolkit' => [
        'version' => '7.2.6',
    ],
    'rematrix' => [
        'version' => '0.2.2',
    ],
];

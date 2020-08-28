import jquery from 'jquery';

// make jquery globally available for all other packages. see:
// https://symfony.com/doc/current/frontend/encore/legacy-applications.html
global.$ = jquery;
global.jQuery = jquery;

import 'bootstrap';

import { createApp } from './app';

let initialized = false;

function init() {
    if (initialized) {
        return;
    }

    initialized = true;

    createApp();
}

$(init);

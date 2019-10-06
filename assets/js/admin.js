/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// replacement of require("@babel/polyfill");
require( ["core-js/stable", 'regenerator-runtime/runtime']);

// any CSS you require will output into a single css file (app.css in this case)
require('../css/admin.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

require('@mindbase/vuejs-bundle/Resources/assets/js/mindbase-vuejs').init();

const BrainappealSshKeyManagement = require('@brainappeal/ssh-key-management-bundle/Resources/assets/js/brainappeal-ssh-key-management');
BrainappealSshKeyManagement.initBrainappealSshKeyManagement();

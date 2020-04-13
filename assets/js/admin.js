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


jQuery(document).ready(function() {
    let $chartContainers = $('.mb-chart-container');
    if ($chartContainers.length > 0) {
        import('./modules/chart').then(({ default: baChart }) => {
            baChart.setUpList($chartContainers);

        }).catch(error => 'An error occurred while loading the chart component');
    }
});
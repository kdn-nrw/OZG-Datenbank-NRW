/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// replacement of require("@babel/polyfill");
require( ["core-js/stable", 'regenerator-runtime/runtime']);

// any CSS you require will output into a single css file (app.css in this case)
require('../css/admin.scss');

// jQuery is included globally outside of webpack!
import $ from 'jquery';
//global.$ = $;
const appOnReady = function() {
    let frontendBody = document.querySelector('.app-fe');
    if (frontendBody) {
        import('./modules/frontend').then(({ default: appFrontend }) => {
            appFrontend.setUp();

        }).catch(error => {
            console.log('An error occurred while loading the frontend component', error);
        });
        require('./modules/bootstrap.offcanvas');
    }
    let chartContainers = document.querySelectorAll('.mb-chart-container');
    if (chartContainers.length > 0) {
        import('./modules/chart').then(({ default: appChart }) => {
            appChart.setUpList(chartContainers);

        }).catch(error => {
            console.log('An error occurred while loading the chart component', error);
        });
    }
    let advancedSelectElements = document.querySelectorAll('select.js-advanced-select');
    if (advancedSelectElements.length > 0) {
        import('./modules/advanced-select').then(({ default: appAdvanceSelect }) => {
            appAdvanceSelect.setUpList(advancedSelectElements);

        }).catch(error => {
            console.log('An error occurred while loading the advanced-select component', error);
        });
    }
    let filterAddLinks = document.querySelectorAll('.js-filter-add');
    let filterSelection = document.getElementById("navbar-filter-selection");
    if (filterAddLinks.length > 0 || filterSelection) {
        import('./modules/filter').then(({ default: appFilter }) => {
            appFilter.setUpAddLinksList(filterAddLinks);
            appFilter.setUpFilterSelectionList(filterSelection);
        }).catch(error => {
            console.log('An error occurred while loading the filter component', error);
        });
    }
    let modalForms = document.querySelectorAll('.js-modal-form');
    if (modalForms.length > 0) {
        import('./modules/modal-form').then(({ default: ModalForm }) => {
            for (let i = 0, n = modalForms.length; i < n; i++) {
                new ModalForm(modalForms[i]);
            }

        }).catch(error => {
            console.log('An error occurred while loading the form component', error);
        });
    }
    let formContainers = document.querySelectorAll('.sonata-ba-form');
    if (formContainers.length > 0 && typeof Admin !== "undefined") {
        import('./modules/form').then(({ default: appForm }) => {
            appForm.setUpList(formContainers);

        }).catch(error => {
            console.log('An error occurred while loading the form component', error);
        });
    }
    import('./modules/common').then(({ default: appCommon }) => {
        appCommon.init();

    }).catch(error => {
        console.log('An error occurred while loading the common component', error);
    });
};

if (
    document.readyState === "complete" ||
    (document.readyState !== "loading" && !document.documentElement.doScroll)
) {
    appOnReady();
} else {
    document.addEventListener("DOMContentLoaded", appOnReady);
}
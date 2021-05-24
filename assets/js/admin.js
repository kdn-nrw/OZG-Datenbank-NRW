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
    jQuery('[data-toggle="popover"]').popover();
    let filterAddLinks = document.querySelectorAll('.js-filter-add');
    if (filterAddLinks.length > 0 && typeof Admin !== "undefined") {
        import('./modules/filter').then(({ default: appFilter }) => {
            appFilter.setUpList(filterAddLinks);

        }).catch(error => {
            console.log('An error occurred while loading the filter component', error);
        });
    }
    let filterSelection = document.getElementById("navbar-filter-selection");
    if (filterSelection) {
        let navbarElement = filterSelection.parentNode;
        let filterBox = document.querySelector(".sonata-filters-box");
        let filterForm = filterBox ? filterBox.querySelector(".sonata-filter-form") : null;
        if (filterBox && filterForm) {
            let checkEmptyState = function(element) {
                let checkHasAtLeastOneChildElement = function(parent) {
                    let children = parent.childNodes;
                    for (let i = 0, n = children.length; i < n; i++) {
                        if (children[i].nodeName !== '#text' && !children[i].classList.contains('hide-empty-block')) {
                            return true;
                        }
                    }
                    return false;
                };
                if (!checkHasAtLeastOneChildElement(element)) {
                    element.classList.add('hide-empty-block');
                    if (element.parentNode) {
                        checkEmptyState(element.parentNode);
                    }
                }
            };
            filterSelection.setAttribute('class', 'app-filter-selection');
            filterBox.parentNode.classList.add('app-container-filter');
            filterBox.parentNode.prepend(filterSelection);
            checkEmptyState(navbarElement);
        }
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
    document.addEventListener('click', function (evt) {
        let link;
        if (evt.target.matches('.js-click-toggle')) {
            link = evt.target;
        } else {
            link = evt.target.closest('.js-click-toggle')
        }
        if (link) {
            evt.preventDefault();
            evt.stopPropagation();
            let toggleElt = document.getElementById(link.dataset.toggle);
            if (toggleElt) {
                toggleElt.classList.remove('updating');
                if (toggleElt.classList.contains('open')) {
                    toggleElt.classList.remove('open');
                    toggleElt.style.display = 'none';
                    link.classList.remove('active');
                } else {
                    let targets = document.querySelectorAll('.js-toggle-target');
                    for (let i = 0, n = targets.length; i < n; i++) {
                        if (targets[i] !== toggleElt) {
                            targets[i].classList.remove('open');
                            targets[i].style.display = 'none';
                        }
                    }
                    toggleElt.classList.add('open');
                    toggleElt.removeAttribute('style');
                    let toggles = document.querySelectorAll('.js-click-toggle');
                    for (let i = 0, n = toggles.length; i < n; i++) {
                        if (toggles[i] !== link) {
                            toggles[i].classList.remove('active');
                        }
                    }
                    link.classList.add('active');
                }
            }
        }
    }, false);
};

if (
    document.readyState === "complete" ||
    (document.readyState !== "loading" && !document.documentElement.doScroll)
) {
    appOnReady();
} else {
    document.addEventListener("DOMContentLoaded", appOnReady);
}
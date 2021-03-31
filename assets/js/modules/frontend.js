/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory();
    } else {
        // Browser globals (root is window)
        // noinspection JSUndefinedPropertyAssignment
        root.appFrontend = factory();
    }
}(this, function () {
    "use strict";
    return {
        setUp: function () {
            let self = this;
            self.init();
        },
        init: function () {
            let self = this;
            document.addEventListener('click', function (evt) {
                let link;
                if (evt.target.matches('.js-search-toggle')) {
                    link = evt.target;
                } else {
                    link = evt.target.closest('.js-search-toggle')
                }
                if (link) {
                    evt.preventDefault();
                    evt.stopPropagation();
                    var target = document.getElementById("suchleiste");
                    if (target.style.display === "none") {
                        target.style.display = "block";
                    } else {
                        target.style.display = "none";
                    }
                }
            }, false);
            //
            let inputElements = document.querySelectorAll('.js-input-form-submit');
            for (let i = 0, n = inputElements.length; i < n; i++) {
                inputElements[i].addEventListener("keydown", function(event) {
                    if (event.keyCode === 13) {
                        document.getElementById(inputElements[i].dataset.target).submit();
                        return false;
                    }
                });
            }
        }
    };
}));

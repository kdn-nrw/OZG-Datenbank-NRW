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
        root.appCommon = factory();
    }
}(this, function () {
    "use strict";
    /**
     * Initialize custom select2 elements:
     * jQuery is loaded globally by Sonata Admin and therefore is not required here!
     */
    return {
        init: function () {
            let self = this;
            self.initClickToggle();
            self.initClickLoad();
            self.initContent(document);
        },
        initContent: function (parentNode) {
            $(parentNode).find('[data-toggle="popover"]').popover();
        },
        initClickLoad: function () {
            let self = this;
            let loadContent = function(sourceNode) {
                let targetNode;
                if (!sourceNode.classList.contains('js-loaded')) {
                    sourceNode.classList.add('js-loaded');
                    if (sourceNode.dataset.target) {
                        targetNode = document.getElementById(sourceNode.dataset.target);
                    } else {
                        targetNode = sourceNode;
                    }
                    self.load(sourceNode.dataset.url, 'GET', function(data) {
                        targetNode.innerHTML = data.content;
                        self.initContent(targetNode);
                    });
                }
            };
            let loadNodes = document.querySelectorAll('.js-init-load');
            if (loadNodes.length > 0) {
                if ('IntersectionObserver' in window &&
                    'IntersectionObserverEntry' in window &&
                    'intersectionRatio' in window.IntersectionObserverEntry.prototype) {
                    let observer = new IntersectionObserver(entries => {
                        entries.forEach(entry => {
                            if (entry.intersectionRatio > 0) {
                                loadContent(entry.target);
                                observer.unobserve(entry.target);
                            }
                        });
                    });

                    for (let i = 0, n = loadNodes.length; i < n; i++) {
                        observer.observe(loadNodes[i]);
                    }
                } else {
                    for (let i = 0, n = loadNodes.length; i < n; i++) {
                        self.loadContent(loadNodes[i]);
                        loadContent(loadNodes[i]);
                    }
                }
            }
            self.onClick('.js-click-load', loadContent);
        },
        initClickToggle: function () {
            let self = this;
            self.onClick('.js-click-toggle', function(link) {
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
            });
        },
        onClick: function (selector, callback) {
            document.addEventListener('click', function (evt) {
                let link;
                if (evt.target.matches(selector)) {
                    link = evt.target;
                } else {
                    link = evt.target.closest(selector)
                }
                if (link) {
                    evt.preventDefault();
                    evt.stopPropagation();
                    callback(link);
                }
            }, false);
        },
        load: function(url, method, callback) {

            let xhr = new XMLHttpRequest();
            // Setup our listener to process completed requests
            xhr.onreadystatechange = function () {
                // In local files, status is 0 upon success in Mozilla Firefox
                if(xhr.readyState === XMLHttpRequest.DONE) {
                    data = null;
                    var status = xhr.status;
                    if (status === 0 || (status >= 200 && status < 400)) {
                        // The request has been completed successfully
                        try {
                            var data = JSON.parse(xhr.responseText);
                            callback(data);
                        } catch(err) {
                            console.log(err.message + " in " + xhr.responseText);
                        }
                    }
                }
            };

            xhr.open(method, url);
            xhr.send();
        },
    };
}));

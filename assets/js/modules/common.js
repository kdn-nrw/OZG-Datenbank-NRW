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
            self.initTabs();
            self.initClickToggle();
            self.initClickLoad();
            self.initContent(document);
        },
        initTabs: function() {
            let self = this;
            // only enable in frontend
            if (document.getElementById('header-top')) {
                let lastScrollHash = null;
                if (window.location.hash) {
                    lastScrollHash = window.location.hash;
                    self.scrollTo(window.location.hash);
                }
                window.addEventListener('scroll', function(event) {
                    if (window.location.hash && lastScrollHash !== window.location.hash) {
                        lastScrollHash = window.location.hash;
                        self.scrollTo(window.location.hash);
                    }
                });
                const hash = location.hash.replace(/^#/, '');  // ^ means starting, meaning only match the first hash
                if (hash) {
                    const $activeTab = $('.nav-tabs a[href="#' + hash + '"]');
                    if ($activeTab.length > 0) {
                        $activeTab.parents('.nav-tabs').find('.tab-item.js-init-load').removeClass('js-init-load').addClass('js-click-load');
                        if ($activeTab.parent().hasClass('js-click-load')) {
                            $activeTab.parent().removeClass('js-click-load').addClass('js-init-load');
                        }
                        $activeTab.tab('show');
                    }
                }
                // Change hash for page-reload
                $('.nav-tabs a').on('shown.bs.tab', function (e) {
                    window.location.hash = e.target.hash;
                })
            }
        },
        initContent: function (parentNode) {
            let self = this;
            $(parentNode).find('[data-toggle="popover"]').popover();
            self.initSortableTables(parentNode);
        },
        initSortableTables: function(parentNode) {
            let sortableTableNodes = parentNode.querySelectorAll('[data-sortable="true"]');
            if (sortableTableNodes.length > 0) {
                // https://stackoverflow.com/questions/14267781/sorting-html-table-with-javascript
                const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

                const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                        v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
                )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

                const sortByHeader = function(tableNode, tableColNodes, th) {
                    const tbody = tableNode.tBodies[0];
                    const newSortAsc = typeof th.asc === "undefined" || !th.asc;
                    tableColNodes.forEach(function(resetTh) {
                        if (resetTh.classList.contains('sort-by-active')) {
                            resetTh.classList.remove('sort-by-active');
                            resetTh.classList.remove('sort-asc');
                            resetTh.classList.remove('sort-desc');
                        }
                    });
                    th.classList.add('sort-by-active');
                    th.classList.add((newSortAsc ? 'sort-asc' : 'sort-desc'));
                    th.asc = newSortAsc;
                    Array.from(tbody.querySelectorAll('tr'))
                        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), newSortAsc))
                        .forEach(tr => tbody.appendChild(tr) );
                };
                for (let i = 0, n = sortableTableNodes.length; i < n; i++) {
                    const tableNode = sortableTableNodes[i];
                    if (!tableNode.classList.contains('table-sortable') && tableNode.tBodies.length === 1) {
                        tableNode.classList.add('table-sortable');
                        const tableColNodes = tableNode.querySelectorAll('th');
                        tableColNodes.forEach(function(sortableTh) {
                            const thHtml = sortableTh.innerHTML.trim();
                            if (thHtml.length > 0) {
                                sortableTh.sortable = true;
                                if (!sortableTh.querySelector('.sort-wrap')) {
                                    sortableTh.innerHTML = '<span class="sort-wrap">' + thHtml + '</span>';
                                }
                            } else {
                                sortableTh.sortable = false;
                            }
                        });
                        tableColNodes.forEach(function(th, index){
                            if (th.sortable) {
                                th.addEventListener('click', (() => {
                                    sortByHeader(tableNode, tableColNodes, th);
                                }));
                                if (index === 0) {
                                    sortByHeader(tableNode, tableColNodes, th);
                                }
                            }
                        });
                    }
                }
            }
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
        scrollTo: function(selector, duration) {
            let self = this;
            let target = selector ? document.querySelector(selector) : null;
            if (target !== null && (typeof target.dataset.scrolling === "undefined" || target.dataset.scrolling !== "1")) {
                target.dataset.scrolling = "1";
                // Wait for swiper initialization
                setTimeout(function() {
                    let targetY = $(target).offset().top;
                    if ($(target).hasClass('tab-pane')) {
                        targetY = $(target).parent().parent().offset().top - 10;
                    }
                    let $offsetParent = $('#header-top');
                    let yOffset = $offsetParent.height();
                    if (typeof duration === "undefined" || !duration) {
                        duration = 200;
                    }

                    $('html, body').stop().animate({
                        'scrollTop': targetY - yOffset
                    }, duration, 'swing', function () {
                        target.dataset.scrolling = "0";
                        let newTargetY = $(target).offset().top;
                        if (selector.indexOf('#') === 0) {
                            window.location.hash = selector;
                        }
                    });
                }, 10);
                return true;
            }
            return false;
        },
    };
}));

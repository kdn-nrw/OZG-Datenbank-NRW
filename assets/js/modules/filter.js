/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
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
        root.appFilter = factory();
    }
}(this, function () {
    "use strict";
    /**
     * Initialize custom select2 elements:
     * jQuery is loaded globally by Sonata Admin and therefore is not required here!
     */
    return {
        setUpList: function (filterAddLinks) {
            let self = this;
            for (let i = 0, n = filterAddLinks.length; i < n; i++) {
                self.initAddLink(filterAddLinks[i]);
            }
            self.init();
        },
        init: function () {
            let self = this;
            document.addEventListener('click', function (evt) {
                let link;
                if (evt.target.matches('.js-filter-add')) {
                    link = evt.target;
                } else {
                    link = evt.target.closest('.js-filter-add')
                }
                if (link) {
                    evt.preventDefault();
                    evt.stopPropagation();
                    let filterToggle = self.getFilterToggle(link);
                    let filterGroup = self.getFilterGroup(filterToggle);
                    if (null !== filterToggle && null !== filterGroup) {
                        if (filterGroup.offsetParent === null) {
                            self.click(filterToggle);
                        }
                        self.groupValue(filterGroup, link.dataset.value);
                        let filter = filterGroup.querySelectorAll('select');
                        $(filter).trigger('change');
                        self.submit(link.dataset.container);
                    }
                }
            }, false);
        },
        initAddLink: function(link) {
            let filterToggle = this.getFilterToggle(link);
            let filterGroup = this.getFilterGroup(filterToggle);
            if (null !== filterGroup) {
                let selectedValues = this.groupValue(filterGroup, null);
                if (selectedValues.indexOf(link.dataset.value) >= 0) {
                    link.style.display = 'none';
                }
            } else {
                link.style.display = 'none';
            }
        },
        getFilterToggle: function(link) {
            let filterToggle = document.querySelector('a.sonata-toggle-filter[filter-target$="'+link.dataset.target+'"]');
            if (filterToggle === null && link.dataset.field) {
                filterToggle = document.querySelector('a.sonata-toggle-filter[filter-target$="'+link.dataset.field+'"]');
            }
            return filterToggle;
        },
        getFilterGroup: function(filterToggle) {
            if (filterToggle !== null) {
                let filterGroupId = filterToggle.getAttribute('filter-target');
                return document.getElementById(filterGroupId);
            }
            return null;
        },
        submit: function(filterContainerId) {
            let filterContainer = document.getElementById(filterContainerId);
            let btn = filterContainer.querySelector('.btn-primary[type="submit"]');
            if (btn) {
                this.click(btn);
            }
        },
        click: function(elt) {
            var clickShow = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
            });
            elt.dispatchEvent(clickShow);
        },
        groupValue: function(filterGroup, value) {
            let selectedValues = [];
            let options = filterGroup.querySelectorAll('select[id$="_value"] option');
            for (let oi = 0, on = options.length; oi < on; oi++) {
                let option = options[oi];
                // noinspection EqualityComparisonWithCoercionJS
                if (!option.selected && value !== null && option.value == value) {
                    option.selected = true;
                }
                if (option.selected) {
                    selectedValues.push(option.value);
                }
            }
            return selectedValues;
        },
    };
}));

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
        setUpAddLinksList: function (filterAddLinks) {
            let self = this;
            if (filterAddLinks.length > 0) {
                for (let i = 0, n = filterAddLinks.length; i < n; i++) {
                    self.initAddLink(filterAddLinks[i]);
                }
                self.initAddLinkOnClick();
            }
        },
        initAddLinkOnClick: function () {
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
        setUpFilterSelectionList: function (filterSelection) {
            let self = this;
            if (filterSelection !== null) {
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
                    let customFilterLinks = filterSelection.querySelectorAll('.js-custom-filter');
                    if (customFilterLinks.length > 0) {
                        for (let i = 0, n = customFilterLinks.length; i < n; i++) {
                             let customFilters = JSON.parse(customFilterLinks[i].dataset.filterValue);
                             if (self.checkCustomFilterActive(filterSelection, filterBox, filterForm, customFilters)) {
                                 customFilterLinks[i].classList.add('active');
                             }
                        }
                        document.addEventListener('click', function (event) {
                            let linkElement = event.target.matches('.js-custom-filter');
                            if (!linkElement) {
                                linkElement = event.target.closest('.js-custom-filter');
                            }
                            // If the clicked element doesn't have the right selector, bail
                            if (!linkElement) return;

                            // Don't follow the link
                            event.preventDefault();

                            // Log the clicked element in the console
                            if (linkElement.dataset) {
                                let customFilters = JSON.parse(linkElement.dataset.filterValue);
                                self.onCustomFilterClicked(filterSelection, filterBox, filterForm, customFilters);
                            }

                        }, false);
                    }
                }
            }
        },
        checkCustomFilterActive: function (filterSelection, filterBox, filterForm, customFilters) {

            let filterId = filterBox.getAttribute('id').replace('filter-container', 'filter');
            let isActive = true;
            Object.keys(customFilters).forEach(function (item) {
                let filterTarget = filterId + '-' + item;
                let itemFilter = document.getElementById(filterTarget);
                if (itemFilter) {
                    let valueField = document.getElementById('filter_'+item+'_value');
                    if (valueField) {
                        if (valueField.tagName.toLowerCase() === 'select') {
                            isActive = isActive && valueField.value == customFilters[item];
                        } else if (valueField.tagName.toLowerCase() === 'input') {
                            isActive = isActive && valueField.value == customFilters[item];
                        }
                    }
                }
            });
            return isActive;
        },
        onCustomFilterClicked: function (filterSelection, filterBox, filterForm, customFilters) {

            let filterId = filterBox.getAttribute('id').replace('filter-container', 'filter');
            //filter-s62d41a9d9c3b7-status
            Object.keys(customFilters).forEach(function (item) {
                let filterTarget = filterId + '-' + item;
                let itemFilter = document.getElementById(filterTarget);
                if (itemFilter) {
                    let selectFilter = filterSelection.querySelector('a[filter-target="' + filterTarget + '"]');
                    if (selectFilter && itemFilter.offsetParent === null) {
                        selectFilter.click();
                    }
                    let valueField = document.getElementById('filter_'+item+'_value');
                    if (valueField) {
                        if (valueField.tagName.toLowerCase() === 'select') {
                            valueField.value = customFilters[item];
                            $(valueField).select2("val", customFilters[item]);
                        } else if (valueField.tagName.toLowerCase() === 'input') {
                            valueField.value = customFilters[item];
                        }
                    }
                }
            });
            filterForm.submit();
        }
    };
}));

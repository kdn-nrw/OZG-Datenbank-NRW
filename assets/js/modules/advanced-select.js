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
        root.appAdvanceSelect = factory();
    }
}(this, function () {
    "use strict";
    /**
     * Initialize custom select2 elements:
     * jQuery and Select2 are loaded globally by Sonata Admin and therefore are not required here!
     */
    return {
        setUpList: function (advancedSelectElements) {
            let self = this;
            for (let i = 0, n = advancedSelectElements.length; i < n; i++) {
                self.initSelect2(advancedSelectElements.item(i));
            }
        },

        initSelect2: function(container) {
            let self = this;
            // @see vendor/sonata-project/admin-bundle/src/Resources/public/Admin.js
            let select                  = jQuery(container);
            let allowClearEnabled       = false;
            let popover                 = select.data('popover');
            let maximumSelectionSize    = null;
            let minimumResultsForSearch = 10;

            select.removeClass('form-control');

            if (select.find('option[value=""]').length || select.attr('data-sonata-select2-allow-clear')==='true') {
                allowClearEnabled = true;
            } else if (select.attr('data-sonata-select2-allow-clear')==='false') {
                allowClearEnabled = false;
            }

            if (select.attr('data-sonata-select2-maximumSelectionSize')) {
                maximumSelectionSize = select.attr('data-sonata-select2-maximumSelectionSize');
            }

            if (select.attr('data-sonata-select2-minimumResultsForSearch')) {
                minimumResultsForSearch = select.attr('data-sonata-select2-minimumResultsForSearch');
            }

            let selectOptions = {
                width: function(){
                    // Select2 v3 and v4 BC. If window.Select2 is defined, then the v3 is installed.
                    // NEXT_MAJOR: Remove Select2 v3 support.
                    return self.getSelect2Width(window.Select2 ? this.element : select);
                },
                dropdownAutoWidth: true,
                minimumResultsForSearch: minimumResultsForSearch,
                allowClear: allowClearEnabled,
                maximumSelectionSize: maximumSelectionSize
            };
            select.select2(selectOptions);

            if (undefined !== popover) {
                select
                    .select2('container')
                    .popover(popover.options)
                ;
            }
            if (select.attr('data-reload-selector')) {
                select.on('change', function (e) {
                    const $reloadSelect = $(select.attr('data-reload-selector')).first();
                    self.updateSelectChoices($reloadSelect, e);
                });
            }
        },
        updateSelectChoices: function($element, event) {
            let self = this;
            let type, changedId;
            if (event.added) {
                type = 'added';
                changedId = event.added.id;
            } else {
                type = 'removed';
                changedId = event.removed.id;
            }
            const postData = {
                changeData: {
                    entityId: $element.attr('data-entity-id'),
                    groupValues: event.val,
                    type: type,
                    groupId: changedId,
                }
            };
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    const result = JSON.parse(this.responseText);
                    $element.select2('destroy');
                    let oldSelected = $element.val();
                    let origSelect = $element.get(0);
                    for (let i = origSelect.options.length - 1; i >= 0; i--) {
                        origSelect.options[i] = null;
                    }
                    for (let i = 0, n = result.data.serviceList.length; i < n; i++){
                        let opt = document.createElement('option');
                        const object = result.data.serviceList[i];
                        opt.value = object.id;
                        opt.innerHTML = object.text;
                        origSelect.appendChild(opt);
                    }
                    if (!oldSelected) {
                        oldSelected = [];
                    }
                    let newSelected = [];
                    if (result.data.removed && result.data.removed.length > 0) {
                        for (let k = 0, kn = oldSelected.length ; k < kn; k++) {
                            let choiceId = parseInt(oldSelected[k]);
                            let found = false;
                            for (let r = 0, rn = result.data.removed.length; r < rn; r++) {
                                if (choiceId === result.data.removed[r].id) {
                                    found = true;
                                    break;
                                }
                            }
                            if (!found) {
                                newSelected.push(choiceId);
                            }
                        }
                    } else if (result.data.added && result.data.added.length > 0) {
                        newSelected = oldSelected;
                        for (let a = 0, an = result.data.added.length ; a < an; a++) {
                            let addedObject = result.data.added[a];
                            if (oldSelected.indexOf(addedObject.id) < 0) {
                                newSelected.push(addedObject.id);
                            }
                        }
                    }
                    $(origSelect).val(newSelected);

                    $(origSelect).select2({
                        width: function(){
                            // Select2 v3 and v4 BC. If window.Select2 is defined, then the v3 is installed.
                            // NEXT_MAJOR: Remove Select2 v3 support.
                            return self.getSelect2Width(window.Select2 ? this.element : select);
                        },
                        dropdownAutoWidth: true,
                        minimumResultsForSearch: 10,
                        allowClear: true,
                        maximumSelectionSize: null
                    });
                }
            }
            let url = $element.attr('data-url');
            if (!url) {
                url = $($element.data('select2').select).attr('data-url');
            }
            xhttp.open("POST", url, true);
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.send(JSON.stringify(postData));
        },
        /** Return the width for simple and sortable select2 element
         * @see vendor/sonata-project/admin-bundle/src/Resources/public/Admin.js  **/
        getSelect2Width: function(element){
            let ereg = /width:(auto|(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc)))/i;

            // this code is an adaptation of select2 code (initContainerWidth function)
            let style = element.attr('style');
            //console.log("main style", style);

            if (style !== undefined) {
                let attrs = style.split(';');

                for (let i = 0, l = attrs.length; i < l; i = i + 1) {
                    let matches = attrs[i].replace(/\s/g, '').match(ereg);
                    if (matches !== null && matches.length >= 1)
                        return matches[1];
                }
            }

            style = element.css('width');
            if (style.indexOf("%") > 0) {
                return style;
            }

            return '100%';
        },
        addLoader: function(container) {
            container.innerHTML = '<div class="chart-loader"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>';
        }
    };
}));

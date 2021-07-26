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
        root.appForm = factory();
    }
}(this, function () {
    "use strict";
    /**
     * Initialize custom select2 elements:
     * jQuery is loaded globally by Sonata Admin and therefore is not required here!
     */
    return {
        setUpList: function (formContainers) {
            let self = this;
            for (let i = 0, n = formContainers.length; i < n; i++) {
                self.initFormContainer(formContainers[i]);
            }
        },
        initFormContainer: function(formContainer) {
            let self = this;
            let copyRowsContainers = formContainer.querySelectorAll('.js-copy-row-values');
            if (copyRowsContainers.length > 0) {
                for (let i = 0, n = copyRowsContainers.length; i < n; i++) {
                    self.initCopyRowContainer(copyRowsContainers[i]);
                }
            }
            self.initFormMeta(formContainer);
        },
        initFormMeta: function(formContainer) {
            let self = this;
            // jQuery is loaded globally by Sonata Admin and therefore is not required here!
            let formMeta = formContainer.querySelector('.app-form-meta');
            if (formMeta) {
                let metaProperties = JSON.parse(formMeta.dataset.meta);
                if (typeof metaProperties === 'object') {
                    let formId = formMeta.dataset.formId;
                    let keys = Object.keys(metaProperties);
                    keys.forEach(function (key) {
                        const idSuffix = formId + '_' + metaProperties[key].property;
                        self.addFormElementMeta(idSuffix, key, metaProperties);
                    });
                }
                jQuery('.js-form-label-popover:not(.initialized)').each(function(){
                    $(this).addClass('initialized');
                    $(this).popover();
                });
            }

        },
        addFormElementMeta: function(idSuffix, key, metaProperties) {
            let self = this;
            const formGroupElt = document.getElementById('sonata-ba-field-container-' + idSuffix);
            let labelElt = null;
            if (formGroupElt) {
                labelElt = formGroupElt.querySelector('.control-label');
                if (!labelElt) {
                    labelElt = formGroupElt.querySelector('.control-label__text');
                }
                if (labelElt && labelElt.querySelector('.field-help') === null) {
                    let description = '';
                    if (metaProperties[key].description) {
                        description = metaProperties[key].description.replace('"', "'").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>');
                    }
                    let helpTitle = (labelElt.textContent).replace(/(<([^>]+)>)/gi, "");
                    let propertyHelpHtml = '\n' +
                        '<span id="meta-help-'+idSuffix+'" class="has-popover">' +
                        '<span class="field-help js-form-label-popover" data-toggle="popover" title="'+helpTitle+'" data-content="'+description+'"' +
                        ' data-html="1" data-trigger="hover" data-placement="top" data-container="#meta-help-'+idSuffix+'">' +
                        ' <i class="fa fa-question-circle" aria-hidden="true"></i>' +
                        '</span>' +
                        '</span>';
                    labelElt.innerHTML = labelElt.innerHTML + propertyHelpHtml;
                }
                if ((typeof metaProperties[key].subMeta) === "object" && metaProperties[key].subMeta !== null) {
                    let subMetaProperties = metaProperties[key].subMeta;
                    let subKeys = Object.keys(subMetaProperties);
                    let maxCollectionOffset = -1;
                    subKeys.forEach(function (subKey) {
                        const subPropertyName = subMetaProperties[subKey].property;
                        if (maxCollectionOffset < 0) {
                            let checkCollectionElt = null;
                            do {
                                ++maxCollectionOffset;
                                let checkSuffix = idSuffix + '_' + maxCollectionOffset + '_' + subPropertyName;
                                checkCollectionElt = document.getElementById('sonata-ba-field-container-' + checkSuffix);
                                if (checkCollectionElt === null) {
                                    --maxCollectionOffset;
                                }
                            } while(checkCollectionElt !== null);
                        }
                        for (let i=0; i<=maxCollectionOffset; i++) {
                            const subIdSuffix = idSuffix + '_' + i + '_' + subPropertyName;
                            self.addFormElementMeta(subIdSuffix, subKey, subMetaProperties);
                        }
                    });
                }
                return true;
            }
            return false;
        },
        initCopyRowContainer: function(copyRowsContainer) {
            let self = this;
            let rows = copyRowsContainer.querySelectorAll('.sonata-collection-row');
            if (rows.length > 0) {
                for (let i = 0, n = rows.length; i < n; i++) {
                    let rowElement = rows[i];
                    rowElement.innerHTML = '<div class="block-copy-paste">'
                        + '<i class="fa fa-files-o action-copy inactive js-row-copy" aria-hidden="true"></i>'
                        + '<i class="fa fa-clipboard action-paste inactive disabled js-row-paste" aria-hidden="true"></i>'
                        + '</div>' + rowElement.innerHTML;
                    copyRowsContainer.addEventListener('click', function (evt) {
                        if (evt.target.matches('.js-row-copy')) {
                            evt.preventDefault();
                            evt.stopPropagation();
                            self.resetCopyPasteContainer(copyRowsContainer, evt.target);
                        }
                        if (evt.target.matches('.js-row-paste')) {
                            evt.preventDefault();
                            evt.stopPropagation();
                            self.pasteValues(copyRowsContainer, evt.target);
                            self.resetCopyPasteContainer(copyRowsContainer, null);
                        }
                    }, false);
                }
            }
        },
        resetCopyPasteContainer: function (copyRowsContainer, activeCopyElt) {
            let copyList = copyRowsContainer.querySelectorAll('.js-row-copy');
            for (let i = 0, n = copyList.length; i < n; i++) {
                if (copyList[i].classList.contains('active')) {
                    copyList[i].classList.remove('active');
                    copyList[i].classList.add('inactive');
                }
                if (copyList[i].classList.contains('disabled')) {
                    copyList[i].classList.remove('disabled');
                }
                if (activeCopyElt) {
                    if (activeCopyElt && activeCopyElt === copyList[i]) {
                        copyList[i].classList.remove('inactive');
                        copyList[i].classList.add('active');
                    } else {
                        copyList[i].classList.add('disabled');
                    }
                }
            }
            let pasteList = copyRowsContainer.querySelectorAll('.js-row-paste');
            for (let i = 0, n = pasteList.length; i < n; i++) {
                if (pasteList[i].classList.contains('active')) {
                    pasteList[i].classList.remove('active');
                    pasteList[i].classList.add('inactive');
                }
                if (pasteList[i].classList.contains('disabled')) {
                    pasteList[i].classList.remove('disabled');
                }
                if (activeCopyElt) {
                    if (activeCopyElt.nextSibling !== pasteList[i]) {
                        pasteList[i].classList.remove('inactive');
                        pasteList[i].classList.add('active');
                    } else {
                        pasteList[i].classList.add('disabled');
                    }
                }
            }
        },
        pasteValues: function (copyRowsContainer, activePasteElt) {
            let activeCopyElt = copyRowsContainer.querySelector('.action-copy.active');
            if (activeCopyElt) {
                let copyRow = activeCopyElt.closest('.sonata-collection-row');
                let pasteRow = activePasteElt.closest('.sonata-collection-row');
                if (copyRow && pasteRow) {
                    let copyFormInputs = copyRow.querySelectorAll('.form-control');
                    let pasteFormInputs = pasteRow.querySelectorAll('.form-control');
                    for (let i = 0, n = copyFormInputs.length; i < n; i++) {
                        if (!copyFormInputs[i].disabled && !pasteFormInputs[i].disabled) {
                            pasteFormInputs[i].value = copyFormInputs[i].value;
                        }
                    }
                }
            }
        }
    };
}));

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

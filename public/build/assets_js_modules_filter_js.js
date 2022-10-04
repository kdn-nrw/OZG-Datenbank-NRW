(self["webpackChunkkdn_ozg"] = self["webpackChunkkdn_ozg"] || []).push([["assets_js_modules_filter_js"],{

/***/ "./assets/js/modules/filter.js":
/*!*************************************!*\
  !*** ./assets/js/modules/filter.js ***!
  \*************************************/
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function (root, factory) {
  if (true) {
    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
		__WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(this, function () {
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
          link = evt.target.closest('.js-filter-add');
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
    initAddLink: function (link) {
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
    getFilterToggle: function (link) {
      let filterToggle = document.querySelector('a.sonata-toggle-filter[filter-target$="' + link.dataset.target + '"]');

      if (filterToggle === null && link.dataset.field) {
        filterToggle = document.querySelector('a.sonata-toggle-filter[filter-target$="' + link.dataset.field + '"]');
      }

      return filterToggle;
    },
    getFilterGroup: function (filterToggle) {
      if (filterToggle !== null) {
        let filterGroupId = filterToggle.getAttribute('filter-target');
        return document.getElementById(filterGroupId);
      }

      return null;
    },
    submit: function (filterContainerId) {
      let filterContainer = document.getElementById(filterContainerId);
      let btn = filterContainer.querySelector('.btn-primary[type="submit"]');

      if (btn) {
        this.click(btn);
      }
    },
    click: function (elt) {
      var clickShow = new MouseEvent('click', {
        bubbles: true,
        cancelable: true,
        view: window
      });
      elt.dispatchEvent(clickShow);
    },
    groupValue: function (filterGroup, value) {
      let selectedValues = [];
      let options = filterGroup.querySelectorAll('select[id$="_value"] option');

      for (let oi = 0, on = options.length; oi < on; oi++) {
        let option = options[oi]; // noinspection EqualityComparisonWithCoercionJS

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
          let checkEmptyState = function (element) {
            let checkHasAtLeastOneChildElement = function (parent) {
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
              } // If the clicked element doesn't have the right selector, bail


              if (!linkElement) return; // Don't follow the link

              event.preventDefault(); // Log the clicked element in the console

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
          let valueField = document.getElementById('filter_' + item + '_value');

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
      let filterId = filterBox.getAttribute('id').replace('filter-container', 'filter'); //filter-s62d41a9d9c3b7-status

      Object.keys(customFilters).forEach(function (item) {
        let filterTarget = filterId + '-' + item;
        let itemFilter = document.getElementById(filterTarget);

        if (itemFilter) {
          let selectFilter = filterSelection.querySelector('a[filter-target="' + filterTarget + '"]');

          if (selectFilter && itemFilter.offsetParent === null) {
            selectFilter.click();
          }

          let valueField = document.getElementById('filter_' + item + '_value');

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
});

/***/ })

}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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LElBQUlHLFVBQUosRUFBZ0I7WUFDWixJQUFJQSxVQUFVLENBQUNDLE9BQVgsQ0FBbUJDLFdBQW5CLE9BQXFDLFFBQXpDLEVBQW1EO2NBQy9DRixVQUFVLENBQUNyRSxLQUFYLEdBQW1Cb0QsYUFBYSxDQUFDYyxJQUFELENBQWhDO2NBQ0EvRCxDQUFDLENBQUNrRSxVQUFELENBQUQsQ0FBY0ksT0FBZCxDQUFzQixLQUF0QixFQUE2QnJCLGFBQWEsQ0FBQ2MsSUFBRCxDQUExQztZQUNILENBSEQsTUFHTyxJQUFJRyxVQUFVLENBQUNDLE9BQVgsQ0FBbUJDLFdBQW5CLE9BQXFDLE9BQXpDLEVBQWtEO2NBQ3JERixVQUFVLENBQUNyRSxLQUFYLEdBQW1Cb0QsYUFBYSxDQUFDYyxJQUFELENBQWhDO1lBQ0g7VUFDSjtRQUNKO01BQ0osQ0FsQkQ7TUFtQkE1QixVQUFVLENBQUNqQyxNQUFYO0lBQ0g7RUFsTUUsQ0FBUDtBQW9NSCxDQXZOQSxDQUFEIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8va2RuX296Zy8uL2Fzc2V0cy9qcy9tb2R1bGVzL2ZpbHRlci5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFRoaXMgZmlsZSBpcyBwYXJ0IG9mIHRoZSBLRE4gT1pHIHBhY2thZ2UuXG4gKlxuICogQGF1dGhvciAgICBHZXJ0IEhhbW1lcyA8aW5mb0BnZXJ0aGFtbWVzLmRlPlxuICogQGNvcHlyaWdodCAyMDIwIEdlcnQgSGFtbWVzXG4gKlxuICogRm9yIHRoZSBmdWxsIGNvcHlyaWdodCBhbmQgbGljZW5zZSBpbmZvcm1hdGlvbiwgcGxlYXNlIHZpZXcgdGhlIExJQ0VOU0VcbiAqIGZpbGUgdGhhdCB3YXMgZGlzdHJpYnV0ZWQgd2l0aCB0aGlzIHNvdXJjZSBjb2RlLlxuICovXG4oZnVuY3Rpb24gKHJvb3QsIGZhY3RvcnkpIHtcbiAgICBpZiAodHlwZW9mIGRlZmluZSA9PT0gJ2Z1bmN0aW9uJyAmJiBkZWZpbmUuYW1kKSB7XG4gICAgICAgIGRlZmluZShmYWN0b3J5KTtcbiAgICB9IGVsc2UgaWYgKHR5cGVvZiBtb2R1bGUgPT09ICdvYmplY3QnICYmIG1vZHVsZS5leHBvcnRzKSB7XG4gICAgICAgIC8vIE5vZGUuIERvZXMgbm90IHdvcmsgd2l0aCBzdHJpY3QgQ29tbW9uSlMsIGJ1dFxuICAgICAgICAvLyBvbmx5IENvbW1vbkpTLWxpa2UgZW52aXJvbm1lbnRzIHRoYXQgc3VwcG9ydCBtb2R1bGUuZXhwb3J0cyxcbiAgICAgICAgLy8gbGlrZSBOb2RlLlxuICAgICAgICBtb2R1bGUuZXhwb3J0cyA9IGZhY3RvcnkoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgICAvLyBCcm93c2VyIGdsb2JhbHMgKHJvb3QgaXMgd2luZG93KVxuICAgICAgICAvLyBub2luc3BlY3Rpb24gSlNVbmRlZmluZWRQcm9wZXJ0eUFzc2lnbm1lbnRcbiAgICAgICAgcm9vdC5hcHBGaWx0ZXIgPSBmYWN0b3J5KCk7XG4gICAgfVxufSh0aGlzLCBmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSBjdXN0b20gc2VsZWN0MiBlbGVtZW50czpcbiAgICAgKiBqUXVlcnkgaXMgbG9hZGVkIGdsb2JhbGx5IGJ5IFNvbmF0YSBBZG1pbiBhbmQgdGhlcmVmb3JlIGlzIG5vdCByZXF1aXJlZCBoZXJlIVxuICAgICAqL1xuICAgIHJldHVybiB7XG4gICAgICAgIHNldFVwQWRkTGlua3NMaXN0OiBmdW5jdGlvbiAoZmlsdGVyQWRkTGlua3MpIHtcbiAgICAgICAgICAgIGxldCBzZWxmID0gdGhpcztcbiAgICAgICAgICAgIGlmIChmaWx0ZXJBZGRMaW5rcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDAsIG4gPSBmaWx0ZXJBZGRMaW5rcy5sZW5ndGg7IGkgPCBuOyBpKyspIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5pbml0QWRkTGluayhmaWx0ZXJBZGRMaW5rc1tpXSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHNlbGYuaW5pdEFkZExpbmtPbkNsaWNrKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGluaXRBZGRMaW5rT25DbGljazogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IHNlbGYgPSB0aGlzO1xuICAgICAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbiAoZXZ0KSB7XG4gICAgICAgICAgICAgICAgbGV0IGxpbms7XG4gICAgICAgICAgICAgICAgaWYgKGV2dC50YXJnZXQubWF0Y2hlcygnLmpzLWZpbHRlci1hZGQnKSkge1xuICAgICAgICAgICAgICAgICAgICBsaW5rID0gZXZ0LnRhcmdldDtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsaW5rID0gZXZ0LnRhcmdldC5jbG9zZXN0KCcuanMtZmlsdGVyLWFkZCcpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmIChsaW5rKSB7XG4gICAgICAgICAgICAgICAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICBldnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBmaWx0ZXJUb2dnbGUgPSBzZWxmLmdldEZpbHRlclRvZ2dsZShsaW5rKTtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGZpbHRlckdyb3VwID0gc2VsZi5nZXRGaWx0ZXJHcm91cChmaWx0ZXJUb2dnbGUpO1xuICAgICAgICAgICAgICAgICAgICBpZiAobnVsbCAhPT0gZmlsdGVyVG9nZ2xlICYmIG51bGwgIT09IGZpbHRlckdyb3VwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoZmlsdGVyR3JvdXAub2Zmc2V0UGFyZW50ID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5jbGljayhmaWx0ZXJUb2dnbGUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5ncm91cFZhbHVlKGZpbHRlckdyb3VwLCBsaW5rLmRhdGFzZXQudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGZpbHRlciA9IGZpbHRlckdyb3VwLnF1ZXJ5U2VsZWN0b3JBbGwoJ3NlbGVjdCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJChmaWx0ZXIpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5zdWJtaXQobGluay5kYXRhc2V0LmNvbnRhaW5lcik7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LCBmYWxzZSk7XG4gICAgICAgIH0sXG4gICAgICAgIGluaXRBZGRMaW5rOiBmdW5jdGlvbihsaW5rKSB7XG4gICAgICAgICAgICBsZXQgZmlsdGVyVG9nZ2xlID0gdGhpcy5nZXRGaWx0ZXJUb2dnbGUobGluayk7XG4gICAgICAgICAgICBsZXQgZmlsdGVyR3JvdXAgPSB0aGlzLmdldEZpbHRlckdyb3VwKGZpbHRlclRvZ2dsZSk7XG4gICAgICAgICAgICBpZiAobnVsbCAhPT0gZmlsdGVyR3JvdXApIHtcbiAgICAgICAgICAgICAgICBsZXQgc2VsZWN0ZWRWYWx1ZXMgPSB0aGlzLmdyb3VwVmFsdWUoZmlsdGVyR3JvdXAsIG51bGwpO1xuICAgICAgICAgICAgICAgIGlmIChzZWxlY3RlZFZhbHVlcy5pbmRleE9mKGxpbmsuZGF0YXNldC52YWx1ZSkgPj0gMCkge1xuICAgICAgICAgICAgICAgICAgICBsaW5rLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBsaW5rLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGdldEZpbHRlclRvZ2dsZTogZnVuY3Rpb24obGluaykge1xuICAgICAgICAgICAgbGV0IGZpbHRlclRvZ2dsZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2Euc29uYXRhLXRvZ2dsZS1maWx0ZXJbZmlsdGVyLXRhcmdldCQ9XCInK2xpbmsuZGF0YXNldC50YXJnZXQrJ1wiXScpO1xuICAgICAgICAgICAgaWYgKGZpbHRlclRvZ2dsZSA9PT0gbnVsbCAmJiBsaW5rLmRhdGFzZXQuZmllbGQpIHtcbiAgICAgICAgICAgICAgICBmaWx0ZXJUb2dnbGUgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdhLnNvbmF0YS10b2dnbGUtZmlsdGVyW2ZpbHRlci10YXJnZXQkPVwiJytsaW5rLmRhdGFzZXQuZmllbGQrJ1wiXScpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIGZpbHRlclRvZ2dsZTtcbiAgICAgICAgfSxcbiAgICAgICAgZ2V0RmlsdGVyR3JvdXA6IGZ1bmN0aW9uKGZpbHRlclRvZ2dsZSkge1xuICAgICAgICAgICAgaWYgKGZpbHRlclRvZ2dsZSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGxldCBmaWx0ZXJHcm91cElkID0gZmlsdGVyVG9nZ2xlLmdldEF0dHJpYnV0ZSgnZmlsdGVyLXRhcmdldCcpO1xuICAgICAgICAgICAgICAgIHJldHVybiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChmaWx0ZXJHcm91cElkKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9LFxuICAgICAgICBzdWJtaXQ6IGZ1bmN0aW9uKGZpbHRlckNvbnRhaW5lcklkKSB7XG4gICAgICAgICAgICBsZXQgZmlsdGVyQ29udGFpbmVyID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoZmlsdGVyQ29udGFpbmVySWQpO1xuICAgICAgICAgICAgbGV0IGJ0biA9IGZpbHRlckNvbnRhaW5lci5xdWVyeVNlbGVjdG9yKCcuYnRuLXByaW1hcnlbdHlwZT1cInN1Ym1pdFwiXScpO1xuICAgICAgICAgICAgaWYgKGJ0bikge1xuICAgICAgICAgICAgICAgIHRoaXMuY2xpY2soYnRuKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgY2xpY2s6IGZ1bmN0aW9uKGVsdCkge1xuICAgICAgICAgICAgdmFyIGNsaWNrU2hvdyA9IG5ldyBNb3VzZUV2ZW50KCdjbGljaycsIHtcbiAgICAgICAgICAgICAgICBidWJibGVzOiB0cnVlLFxuICAgICAgICAgICAgICAgIGNhbmNlbGFibGU6IHRydWUsXG4gICAgICAgICAgICAgICAgdmlldzogd2luZG93XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGVsdC5kaXNwYXRjaEV2ZW50KGNsaWNrU2hvdyk7XG4gICAgICAgIH0sXG4gICAgICAgIGdyb3VwVmFsdWU6IGZ1bmN0aW9uKGZpbHRlckdyb3VwLCB2YWx1ZSkge1xuICAgICAgICAgICAgbGV0IHNlbGVjdGVkVmFsdWVzID0gW107XG4gICAgICAgICAgICBsZXQgb3B0aW9ucyA9IGZpbHRlckdyb3VwLnF1ZXJ5U2VsZWN0b3JBbGwoJ3NlbGVjdFtpZCQ9XCJfdmFsdWVcIl0gb3B0aW9uJyk7XG4gICAgICAgICAgICBmb3IgKGxldCBvaSA9IDAsIG9uID0gb3B0aW9ucy5sZW5ndGg7IG9pIDwgb247IG9pKyspIHtcbiAgICAgICAgICAgICAgICBsZXQgb3B0aW9uID0gb3B0aW9uc1tvaV07XG4gICAgICAgICAgICAgICAgLy8gbm9pbnNwZWN0aW9uIEVxdWFsaXR5Q29tcGFyaXNvbldpdGhDb2VyY2lvbkpTXG4gICAgICAgICAgICAgICAgaWYgKCFvcHRpb24uc2VsZWN0ZWQgJiYgdmFsdWUgIT09IG51bGwgJiYgb3B0aW9uLnZhbHVlID09IHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbi5zZWxlY3RlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmIChvcHRpb24uc2VsZWN0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZWN0ZWRWYWx1ZXMucHVzaChvcHRpb24udmFsdWUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBzZWxlY3RlZFZhbHVlcztcbiAgICAgICAgfSxcbiAgICAgICAgc2V0VXBGaWx0ZXJTZWxlY3Rpb25MaXN0OiBmdW5jdGlvbiAoZmlsdGVyU2VsZWN0aW9uKSB7XG4gICAgICAgICAgICBsZXQgc2VsZiA9IHRoaXM7XG4gICAgICAgICAgICBpZiAoZmlsdGVyU2VsZWN0aW9uICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgbGV0IG5hdmJhckVsZW1lbnQgPSBmaWx0ZXJTZWxlY3Rpb24ucGFyZW50Tm9kZTtcbiAgICAgICAgICAgICAgICBsZXQgZmlsdGVyQm94ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcIi5zb25hdGEtZmlsdGVycy1ib3hcIik7XG4gICAgICAgICAgICAgICAgbGV0IGZpbHRlckZvcm0gPSBmaWx0ZXJCb3ggPyBmaWx0ZXJCb3gucXVlcnlTZWxlY3RvcihcIi5zb25hdGEtZmlsdGVyLWZvcm1cIikgOiBudWxsO1xuICAgICAgICAgICAgICAgIGlmIChmaWx0ZXJCb3ggJiYgZmlsdGVyRm9ybSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgY2hlY2tFbXB0eVN0YXRlID0gZnVuY3Rpb24oZWxlbWVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGNoZWNrSGFzQXRMZWFzdE9uZUNoaWxkRWxlbWVudCA9IGZ1bmN0aW9uKHBhcmVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxldCBjaGlsZHJlbiA9IHBhcmVudC5jaGlsZE5vZGVzO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwLCBuID0gY2hpbGRyZW4ubGVuZ3RoOyBpIDwgbjsgaSsrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjaGlsZHJlbltpXS5ub2RlTmFtZSAhPT0gJyN0ZXh0JyAmJiAhY2hpbGRyZW5baV0uY2xhc3NMaXN0LmNvbnRhaW5zKCdoaWRlLWVtcHR5LWJsb2NrJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIWNoZWNrSGFzQXRMZWFzdE9uZUNoaWxkRWxlbWVudChlbGVtZW50KSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVsZW1lbnQuY2xhc3NMaXN0LmFkZCgnaGlkZS1lbXB0eS1ibG9jaycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChlbGVtZW50LnBhcmVudE5vZGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2hlY2tFbXB0eVN0YXRlKGVsZW1lbnQucGFyZW50Tm9kZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJTZWxlY3Rpb24uc2V0QXR0cmlidXRlKCdjbGFzcycsICdhcHAtZmlsdGVyLXNlbGVjdGlvbicpO1xuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJCb3gucGFyZW50Tm9kZS5jbGFzc0xpc3QuYWRkKCdhcHAtY29udGFpbmVyLWZpbHRlcicpO1xuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJCb3gucGFyZW50Tm9kZS5wcmVwZW5kKGZpbHRlclNlbGVjdGlvbik7XG4gICAgICAgICAgICAgICAgICAgIGNoZWNrRW1wdHlTdGF0ZShuYXZiYXJFbGVtZW50KTtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGN1c3RvbUZpbHRlckxpbmtzID0gZmlsdGVyU2VsZWN0aW9uLnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy1jdXN0b20tZmlsdGVyJyk7XG4gICAgICAgICAgICAgICAgICAgIGlmIChjdXN0b21GaWx0ZXJMaW5rcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKGxldCBpID0gMCwgbiA9IGN1c3RvbUZpbHRlckxpbmtzLmxlbmd0aDsgaSA8IG47IGkrKykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZXQgY3VzdG9tRmlsdGVycyA9IEpTT04ucGFyc2UoY3VzdG9tRmlsdGVyTGlua3NbaV0uZGF0YXNldC5maWx0ZXJWYWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChzZWxmLmNoZWNrQ3VzdG9tRmlsdGVyQWN0aXZlKGZpbHRlclNlbGVjdGlvbiwgZmlsdGVyQm94LCBmaWx0ZXJGb3JtLCBjdXN0b21GaWx0ZXJzKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY3VzdG9tRmlsdGVyTGlua3NbaV0uY2xhc3NMaXN0LmFkZCgnYWN0aXZlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGxpbmtFbGVtZW50ID0gZXZlbnQudGFyZ2V0Lm1hdGNoZXMoJy5qcy1jdXN0b20tZmlsdGVyJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFsaW5rRWxlbWVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaW5rRWxlbWVudCA9IGV2ZW50LnRhcmdldC5jbG9zZXN0KCcuanMtY3VzdG9tLWZpbHRlcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBJZiB0aGUgY2xpY2tlZCBlbGVtZW50IGRvZXNuJ3QgaGF2ZSB0aGUgcmlnaHQgc2VsZWN0b3IsIGJhaWxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoIWxpbmtFbGVtZW50KSByZXR1cm47XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBEb24ndCBmb2xsb3cgdGhlIGxpbmtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gTG9nIHRoZSBjbGlja2VkIGVsZW1lbnQgaW4gdGhlIGNvbnNvbGVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAobGlua0VsZW1lbnQuZGF0YXNldCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZXQgY3VzdG9tRmlsdGVycyA9IEpTT04ucGFyc2UobGlua0VsZW1lbnQuZGF0YXNldC5maWx0ZXJWYWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYub25DdXN0b21GaWx0ZXJDbGlja2VkKGZpbHRlclNlbGVjdGlvbiwgZmlsdGVyQm94LCBmaWx0ZXJGb3JtLCBjdXN0b21GaWx0ZXJzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIGZhbHNlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgY2hlY2tDdXN0b21GaWx0ZXJBY3RpdmU6IGZ1bmN0aW9uIChmaWx0ZXJTZWxlY3Rpb24sIGZpbHRlckJveCwgZmlsdGVyRm9ybSwgY3VzdG9tRmlsdGVycykge1xuXG4gICAgICAgICAgICBsZXQgZmlsdGVySWQgPSBmaWx0ZXJCb3guZ2V0QXR0cmlidXRlKCdpZCcpLnJlcGxhY2UoJ2ZpbHRlci1jb250YWluZXInLCAnZmlsdGVyJyk7XG4gICAgICAgICAgICBsZXQgaXNBY3RpdmUgPSB0cnVlO1xuICAgICAgICAgICAgT2JqZWN0LmtleXMoY3VzdG9tRmlsdGVycykuZm9yRWFjaChmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgICAgICAgICAgIGxldCBmaWx0ZXJUYXJnZXQgPSBmaWx0ZXJJZCArICctJyArIGl0ZW07XG4gICAgICAgICAgICAgICAgbGV0IGl0ZW1GaWx0ZXIgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChmaWx0ZXJUYXJnZXQpO1xuICAgICAgICAgICAgICAgIGlmIChpdGVtRmlsdGVyKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCB2YWx1ZUZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ZpbHRlcl8nK2l0ZW0rJ192YWx1ZScpO1xuICAgICAgICAgICAgICAgICAgICBpZiAodmFsdWVGaWVsZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHZhbHVlRmllbGQudGFnTmFtZS50b0xvd2VyQ2FzZSgpID09PSAnc2VsZWN0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzQWN0aXZlID0gaXNBY3RpdmUgJiYgdmFsdWVGaWVsZC52YWx1ZSA9PSBjdXN0b21GaWx0ZXJzW2l0ZW1dO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICh2YWx1ZUZpZWxkLnRhZ05hbWUudG9Mb3dlckNhc2UoKSA9PT0gJ2lucHV0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzQWN0aXZlID0gaXNBY3RpdmUgJiYgdmFsdWVGaWVsZC52YWx1ZSA9PSBjdXN0b21GaWx0ZXJzW2l0ZW1dO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICByZXR1cm4gaXNBY3RpdmU7XG4gICAgICAgIH0sXG4gICAgICAgIG9uQ3VzdG9tRmlsdGVyQ2xpY2tlZDogZnVuY3Rpb24gKGZpbHRlclNlbGVjdGlvbiwgZmlsdGVyQm94LCBmaWx0ZXJGb3JtLCBjdXN0b21GaWx0ZXJzKSB7XG5cbiAgICAgICAgICAgIGxldCBmaWx0ZXJJZCA9IGZpbHRlckJveC5nZXRBdHRyaWJ1dGUoJ2lkJykucmVwbGFjZSgnZmlsdGVyLWNvbnRhaW5lcicsICdmaWx0ZXInKTtcbiAgICAgICAgICAgIC8vZmlsdGVyLXM2MmQ0MWE5ZDljM2I3LXN0YXR1c1xuICAgICAgICAgICAgT2JqZWN0LmtleXMoY3VzdG9tRmlsdGVycykuZm9yRWFjaChmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgICAgICAgICAgIGxldCBmaWx0ZXJUYXJnZXQgPSBmaWx0ZXJJZCArICctJyArIGl0ZW07XG4gICAgICAgICAgICAgICAgbGV0IGl0ZW1GaWx0ZXIgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChmaWx0ZXJUYXJnZXQpO1xuICAgICAgICAgICAgICAgIGlmIChpdGVtRmlsdGVyKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBzZWxlY3RGaWx0ZXIgPSBmaWx0ZXJTZWxlY3Rpb24ucXVlcnlTZWxlY3RvcignYVtmaWx0ZXItdGFyZ2V0PVwiJyArIGZpbHRlclRhcmdldCArICdcIl0nKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHNlbGVjdEZpbHRlciAmJiBpdGVtRmlsdGVyLm9mZnNldFBhcmVudCA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZWN0RmlsdGVyLmNsaWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgbGV0IHZhbHVlRmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZmlsdGVyXycraXRlbSsnX3ZhbHVlJyk7XG4gICAgICAgICAgICAgICAgICAgIGlmICh2YWx1ZUZpZWxkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodmFsdWVGaWVsZC50YWdOYW1lLnRvTG93ZXJDYXNlKCkgPT09ICdzZWxlY3QnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWVGaWVsZC52YWx1ZSA9IGN1c3RvbUZpbHRlcnNbaXRlbV07XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh2YWx1ZUZpZWxkKS5zZWxlY3QyKFwidmFsXCIsIGN1c3RvbUZpbHRlcnNbaXRlbV0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICh2YWx1ZUZpZWxkLnRhZ05hbWUudG9Mb3dlckNhc2UoKSA9PT0gJ2lucHV0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlRmllbGQudmFsdWUgPSBjdXN0b21GaWx0ZXJzW2l0ZW1dO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBmaWx0ZXJGb3JtLnN1Ym1pdCgpO1xuICAgICAgICB9XG4gICAgfTtcbn0pKTtcbiJdLCJuYW1lcyI6WyJyb290IiwiZmFjdG9yeSIsImRlZmluZSIsImFtZCIsIm1vZHVsZSIsImV4cG9ydHMiLCJhcHBGaWx0ZXIiLCJzZXRVcEFkZExpbmtzTGlzdCIsImZpbHRlckFkZExpbmtzIiwic2VsZiIsImxlbmd0aCIsImkiLCJuIiwiaW5pdEFkZExpbmsiLCJpbml0QWRkTGlua09uQ2xpY2siLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJldnQiLCJsaW5rIiwidGFyZ2V0IiwibWF0Y2hlcyIsImNsb3Nlc3QiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsImZpbHRlclRvZ2dsZSIsImdldEZpbHRlclRvZ2dsZSIsImZpbHRlckdyb3VwIiwiZ2V0RmlsdGVyR3JvdXAiLCJvZmZzZXRQYXJlbnQiLCJjbGljayIsImdyb3VwVmFsdWUiLCJkYXRhc2V0IiwidmFsdWUiLCJmaWx0ZXIiLCJxdWVyeVNlbGVjdG9yQWxsIiwiJCIsInRyaWdnZXIiLCJzdWJtaXQiLCJjb250YWluZXIiLCJzZWxlY3RlZFZhbHVlcyIsImluZGV4T2YiLCJzdHlsZSIsImRpc3BsYXkiLCJxdWVyeVNlbGVjdG9yIiwiZmllbGQiLCJmaWx0ZXJHcm91cElkIiwiZ2V0QXR0cmlidXRlIiwiZ2V0RWxlbWVudEJ5SWQiLCJmaWx0ZXJDb250YWluZXJJZCIsImZpbHRlckNvbnRhaW5lciIsImJ0biIsImVsdCIsImNsaWNrU2hvdyIsIk1vdXNlRXZlbnQiLCJidWJibGVzIiwiY2FuY2VsYWJsZSIsInZpZXciLCJ3aW5kb3ciLCJkaXNwYXRjaEV2ZW50Iiwib3B0aW9ucyIsIm9pIiwib24iLCJvcHRpb24iLCJzZWxlY3RlZCIsInB1c2giLCJzZXRVcEZpbHRlclNlbGVjdGlvbkxpc3QiLCJmaWx0ZXJTZWxlY3Rpb24iLCJuYXZiYXJFbGVtZW50IiwicGFyZW50Tm9kZSIsImZpbHRlckJveCIsImZpbHRlckZvcm0iLCJjaGVja0VtcHR5U3RhdGUiLCJlbGVtZW50IiwiY2hlY2tIYXNBdExlYXN0T25lQ2hpbGRFbGVtZW50IiwicGFyZW50IiwiY2hpbGRyZW4iLCJjaGlsZE5vZGVzIiwibm9kZU5hbWUiLCJjbGFzc0xpc3QiLCJjb250YWlucyIsImFkZCIsInNldEF0dHJpYnV0ZSIsInByZXBlbmQiLCJjdXN0b21GaWx0ZXJMaW5rcyIsImN1c3RvbUZpbHRlcnMiLCJKU09OIiwicGFyc2UiLCJmaWx0ZXJWYWx1ZSIsImNoZWNrQ3VzdG9tRmlsdGVyQWN0aXZlIiwiZXZlbnQiLCJsaW5rRWxlbWVudCIsIm9uQ3VzdG9tRmlsdGVyQ2xpY2tlZCIsImZpbHRlcklkIiwicmVwbGFjZSIsImlzQWN0aXZlIiwiT2JqZWN0Iiwia2V5cyIsImZvckVhY2giLCJpdGVtIiwiZmlsdGVyVGFyZ2V0IiwiaXRlbUZpbHRlciIsInZhbHVlRmllbGQiLCJ0YWdOYW1lIiwidG9Mb3dlckNhc2UiLCJzZWxlY3RGaWx0ZXIiLCJzZWxlY3QyIl0sInNvdXJjZVJvb3QiOiIifQ==
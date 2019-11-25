$(document).ready(function () {
    var appSearch = {
        $container: null,
        $searchForm: null,
        $activeFilter: null,
        init: function ($container) {
            var self = this;
            self.$container = $container;
            self.$searchForm = $container.find('#search-admin-builder');
            var $entityChoice = $container.find('#search_adminId');
            self.toggleAdminFilters($entityChoice, true);
            $entityChoice.on('change', function () {
                self.toggleAdminFilters($(this), false);
            });
            self.$searchForm.find('.js-text-edit').on('click', function(evt){
                evt.preventDefault();
                $(this).parent().hide();
                $(this).parent().next().show();
            });
            self.$searchForm.find('.js-update-text').on('click', function(evt){
                evt.preventDefault();
                self.$searchForm.find('.js-edit').hide();
                self.$searchForm.find('.js-text').show();
                self.updateSearchForm();
            });
        },
        toggleAdminFilters: function ($entityChoice, isInit) {
            var self = this;
            var selectedAdmin = $entityChoice.val();
            var selectedAdminInternal;
            if (selectedAdmin) {
                selectedAdmin = selectedAdmin.trim()
                selectedAdminInternal = selectedAdmin.replace(/\\/g, "-").toLowerCase();
            }
            self.$container.find('.search-admin-filter').hide().removeClass('active');
            if (selectedAdminInternal) {
                var $activeFilter = self.$container.find('#'+selectedAdminInternal);
                self.$activeFilter = $activeFilter;
                self.$container.find('#'+selectedAdminInternal).show().addClass('active');
                if (!$activeFilter.hasClass('js-on-change')) {
                    $activeFilter.find('.js-filter-field select, .js-filter-field input').on('change', function(){
                        var $fieldAdmin = $(this).parents('.search-admin-filter').first();
                        if ($fieldAdmin.hasClass('active')) {
                            self.updateSearchForm();
                        }
                    });
                }
                if (isInit) {
                    self.updateFilterForm();
                } else {
                    self.updateSearchForm();
                }
            } else if(self.$activeFilter) {
                self.$activeFilter = null;
                self.updateSearchForm();
            }
        },
        updateFilterForm: function() {
            var self = this;
            var query = self.$searchForm.find('#search_queryString').val().trim();
            if (query) {
                var vars = query.split('&');
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split('=');
                    var field = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1]);
                    if (value) {
                        var $field = self.$activeFilter.find('.js-filter-row select[name="'+field+'"]');
                        if ($field.length > 0) {
                            $field.find("option[value='" + value + "']").prop("selected", true);
                            $field.select2('val', $field.val());
                        } else {
                            $field = self.$activeFilter.find('.js-filter-row input[name="'+field+'"]');
                            $field.val(value);
                        }
                    }
                }
                self.$searchForm.find('.js-text-edit').hide();
                self.$searchForm.find('.js-text').hide();
                self.$searchForm.find('.js-edit').show();
                self.$searchForm.find('.js-search-active-toggle').show();
            }
        },
        updateSearchForm: function() {
            var self = this;
            if (self.$activeFilter) {
                var $entityChoice = self.$container.find('#search_adminId');
                var searchText = $entityChoice.parents('.form-group').first().find('label').text() + ' ';
                searchText += $entityChoice.find('option:selected').text();
                var countFilters = 0;
                self.$activeFilter.find('.js-filter-row').each(function(){
                    var $filterRow = $(this);
                    var fieldVal = '';
                    var $field = $filterRow.find('.js-filter-field select').first();
                    if ($field.length > 0) {
                        $field.find('option:selected').each(function(){
                            fieldVal += (fieldVal ? ' oder ' : '') + $(this).text();
                        });
                    } else {
                        $field = $filterRow.find('.js-filter-field input').first();
                        fieldVal = $field.val();
                    }
                    if (fieldVal) {
                        var label = $filterRow.find('.js-filter-text').text().trim();
                        searchText += (countFilters > 0) ? ' und mit ' : ' mit ';
                        searchText += label + ' ';
                        if ($filterRow.find('.advanced-filter').is(':visible')) {
                            var $advSelect = $filterRow.find('.advanced-filter select');
                            var $selected = $advSelect.find('option:selected');
                            if ($selected.length > 0) {
                                searchText += $selected.text().trim() + ' ';
                            }
                        }
                        searchText += fieldVal;
                        ++countFilters;
                    }
                });
                self.$searchForm.find('#search_route').val(self.$activeFilter.data('filter-route'));
                self.$searchForm.find('.js-text').text(searchText);
                var $filterTextField = self.$searchForm.find('#search_description');
                if (!$filterTextField.is(':visible')) {
                    $filterTextField.val(searchText);
                }
                self.$searchForm.find('.js-text-edit').show();
                self.$searchForm.find('.js-search-active-toggle').show();
                var $filterForm = self.$activeFilter.find('form');
                self.$searchForm.find('#search_queryString').val($filterForm.serialize());
            } else {
                self.$searchForm.find('#search_route').val('');
                self.$searchForm.find('.js-text').text('');
                self.$searchForm.find('.js-text-edit').hide();
                self.$searchForm.find('.js-search-active-toggle').hide();
                self.$searchForm.find('#search_queryString').val('');
            }
        }
    };
    var $searchContainer = $('#search-admin-list');
    if ($searchContainer.length > 0) {
        appSearch.init($searchContainer);
    }
});
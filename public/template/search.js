$(document).ready(function () {
    var appSearch = {
        init: function ($container) {
            var self = this;
            var $entityChoice = $container.find('#search-entity-type');
            self.toggleAdminFilters($container, $entityChoice);
            $entityChoice.on('change', function () {
                self.toggleAdminFilters($container, $(this));
            });
        },
        toggleAdminFilters: function ($container, $entityChoice) {
            var $selectedChoice = $entityChoice.find('option:selected');
            $container.find('.search-admin-filter').hide();
            if ($selectedChoice.length > 0) {
                $container.find('#'+$selectedChoice.data('admin-id')).show();
            }
        }
    };
    var $searchContainer = $('#search-admin-list');
    if ($searchContainer.length > 0) {
        appSearch.init($searchContainer);
    }
});
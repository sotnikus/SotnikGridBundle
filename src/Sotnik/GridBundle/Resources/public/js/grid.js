(function (){
    var sotnikGrid = {

        init: function()
        {
            this.pageParameter = 'page';
            this.sortParameter = 'sort';
            this.perPageParameter = 'per-page';

            this.bindFilterFormSubmit();
            this.bindFilterReset();
            this.bindSelectFilter();
            this.bindShowHideFilter();
            this.bindGoToPage();
            this.bindPerPageSelection();
        },

        bindFilterFormSubmit: function()
        {
            var that = this;

            $(".sotnik-grid-filter form").submit(function(e){
                var url = $(this).attr("action");

                $(".sotnik-grid-filter form").find('.sotnik-grid-column-filter-value input, .sotnik-grid-column-filter-value select').filter(function(){
                    return $.trim(this.value).length == 0;
                }).prop('disabled',true);

                //few grids per page
                if ($(".sotnik-grid-filter form").length > 1) {
                    e.preventDefault();
                    var gridId = $(this).data('grid-id');
                    var queryString = '';

                    $(".sotnik-grid-filter form").each(function(){

                        var formValues = $(this).serialize(),
                            formGridId = $(this).data('grid-id');

                        if (formGridId != gridId) {
                            var sortParamValue = that.getParameterByName(formGridId + that.sortParameter);
                            if (sortParamValue != '') {
                                formValues += '&' + formGridId + that.sortParameter + '=' + sortParamValue;
                            }

                            var pageParamValue = that.getParameterByName(formGridId + that.pageParameter);
                            if (pageParamValue != '') {
                                formValues += '&' + formGridId + that.pageParameter + '=' + pageParamValue;
                            }
                        }
                        queryString = queryString + '&' +formValues;
                    });

                    location.href = url + '?' + queryString.replace(/^&+/, '').replace(/&+$/, '');
                }

                return true;
            })
        },

        bindFilterReset: function()
        {
            var that = this;

            $(".sotnik-grid-filter-reset").on('click', function(e) {
                //few grids per page
                if ($(".sotnik-grid-filter form").length > 1) {
                    e.preventDefault();

                    var form = $(this).parents(".sotnik-grid-filter").find("form"),
                        gridId = form.data('grid-id'),
                        queryString = '',
                        url = form.attr('action');

                    $(".sotnik-grid-filter form").find('.sotnik-grid-column-filter-value input, .sotnik-grid-column-filter-value select').filter(function(){
                        return $.trim(this.value).length == 0;
                    }).prop('disabled',true);

                    $(".sotnik-grid-filter form").each(function() {
                        var formGridId = $(this).data('grid-id');

                        if (formGridId != gridId) {
                            var formValues = $(this).serialize();

                            var sortParamValue = that.getParameterByName(formGridId + that.sortParameter);
                            if (sortParamValue != '') {
                                formValues += '&' + formGridId + that.sortParameter + '=' + sortParamValue;
                            }

                            var pageParamValue = that.getParameterByName(formGridId + that.pageParameter);
                            if (pageParamValue != '') {
                                formValues += '&' + formGridId + that.pageParameter + '=' + pageParamValue;
                            }

                            queryString = queryString + '&' +formValues;
                        }

                    });

                    location.href = url + '?' + queryString.replace(/^&+/, '').replace(/&+$/, '');
                }
            })
        },

        bindSelectFilter: function()
        {
            $('.sotnik-grid-column-filter-select').on('change', function() {
                var filterContainer = $(this).parents('.sotnik-grid-column-filter'),
                    index = $(this).find('option:selected').index();

                filterContainer.find('.sotnik-grid-column-filter-value input, .sotnik-grid-column-filter-value select').val('');
                filterContainer.find('.sotnik-grid-column-filter-value').hide();
                filterContainer.find('.sotnik-grid-column-filter-value:eq(' + index + ')').show();
            });
        },

        bindShowHideFilter: function()
        {
            $('.sotnik-grid-filter-open').on('click', function(){
                var filters = $(this).parents('.sotnik-grid-filter').find('.sotnik-grid-filter-filters');

                if (filters.is(':hidden')) {
                    filters.show();
                } else {
                    filters.hide();
                }
            });
        },

        bindGoToPage: function()
        {
            var that = this;

            $('.sotnik-grid-pagination-go-to-page').on('submit', function(e) {
                e.preventDefault();

                var url = $(this).attr('action'),
                    value =  parseInt($(this).find('input').val()),
                    gridId = $(this).parents(".sotnik-grid-pagination-pagination").data("grid-id");

                if (value > 0) {
                    url = that.updateQueryStringParameter(url, gridId + that.pageParameter, value);
                    location.href = url;
                }
            });
        },

        bindPerPageSelection: function() {
            var that = this;

            $('.sotnik-grid-pagination-per-page-selection').on('change', function() {

                var gridId = $(this).parents(".sotnik-grid-pagination-pagination").data("grid-id"),
                    value = $(this).val();

                if (value > 0) {
                    url = that.updateQueryStringParameter(location.href, gridId + that.perPageParameter, value);
                    location.href = url;
                }
            });
        },

        updateQueryStringParameter: function (uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            }
            else {
                return uri + separator + key + "=" + value;
            }
        },

        getParameterByName: function(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }

    sotnikGrid.init();

})();

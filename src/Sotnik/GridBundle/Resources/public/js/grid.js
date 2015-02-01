(function (){
    var sotnikGrid = {

        init: function()
        {
            this.pageParameter = $('.sotnik-grid-main').first().data('page-param');
            this.sortParameter = $('.sotnik-grid-main').first().data('sort-param');
            this.perPageParameter = $('.sotnik-grid-main').first().data('per-page-param');

            this.bindFilterFormSubmit();
            this.bindFilterReset();
            this.bindSelectFilter();
            this.bindShowHideFilter();
            this.bindGoToPage();
            this.bindPerPageSelection();
            this.bindBatchCheckboxClick();
            this.bindCheckAllBatchCheckboxes();
            this.bindCheckboxShiftCheck();
            this.bindSubmitBatchActions();
            this.bindClickActionLink();
            this.initDateTimePicker();

            this.lastChecked = null;
        },

        bindFilterFormSubmit: function()
        {
            var that = this;

            $(".sotnik-grid-filter form").submit(function(e){
                var url = $(this).attr("action");

                $(".sotnik-grid-filter form").find('.sotnik-grid-column-filter-value input, .sotnik-grid-column-filter-value select').filter(function(){
                    return $.trim(this.value).length == 0;
                }).prop('disabled',true);

                e.preventDefault();
                var gridId = $(this).data('grid-id');
                var queryString = that.getQueryStringAfterApplyFilter(gridId, true);
                var urlParams = '?' + queryString.replace(/^&+/, '').replace(/&+$/, '');

                location.href = url + urlParams.replace(/\?+$/, '');

                return true;
            })
        },

        bindFilterReset: function()
        {
            var that = this;

            $(".sotnik-grid-filter-reset").on('click', function(e) {

                e.preventDefault();

                var form = $(this).parents(".sotnik-grid-filter").find("form"),
                    gridId = form.data('grid-id'),
                    url = form.attr('action');

                $(".sotnik-grid-filter form").find('.sotnik-grid-column-filter-value input, .sotnik-grid-column-filter-value select').filter(function(){
                    return $.trim(this.value).length == 0;
                }).prop('disabled',true);

                var queryString = that.getQueryStringAfterApplyFilter(gridId, false);
                var urlParams = '?' + queryString.replace(/^&+/, '').replace(/&+$/, '');

                location.href = url + urlParams.replace(/\?+$/, '');

            })
        },

        getQueryStringAfterApplyFilter: function(gridId, submitFilerForm)
        {
            var queryString = '',
                that = this;

            $(".sotnik-grid-filter form").each(function() {
                var formGridId = $(this).data('grid-id'),
                    formValues = '';

                if (submitFilerForm) {
                    formValues += $(this).serialize();
                }

                if (formGridId != gridId) {
                    if (!submitFilerForm) {
                        formValues += $(this).serialize();
                    }

                    var sortParamValue = that.getParameterByName(formGridId + that.sortParameter);
                    if (sortParamValue != '') {
                        formValues += '&' + formGridId + that.sortParameter + '=' + sortParamValue;
                    }

                    var pageParamValue = that.getParameterByName(formGridId + that.pageParameter);
                    if (pageParamValue != '') {
                        formValues += '&' + formGridId + that.pageParameter + '=' + pageParamValue;
                    }
                }

                var perPageParamValue = that.getParameterByName(formGridId + that.perPageParameter);
                if (perPageParamValue != '') {
                    formValues += '&' + formGridId + that.perPageParameter + '=' + perPageParamValue;
                }

                if (formValues.length > 0) {
                    queryString = queryString + '&' +formValues.replace(/^&+/, '');
                }

            });

            return queryString;
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
            $('.sotnik-grid-filter-open a').on('click', function(){
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

        bindPerPageSelection: function()
        {
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

        updateQueryStringParameter: function (uri, key, value)
        {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            }
            else {
                return uri + separator + key + "=" + value;
            }
        },

        getParameterByName: function(name)
        {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        },

        bindBatchCheckboxClick: function()
        {
            var that = this;

            $('.sotnik-grid-batch-checkbox').on('click', function(){
                that.checkAtListOneBatchCheckboxChecked($(this).parents('.sotnik-grid-main'));
            });
        },

        checkAtListOneBatchCheckboxChecked: function($grid)
        {
            if ($grid.find('.sotnik-grid-batch-checkbox:checked').length > 0) {
                $grid.find('.sotnik-grid-batch-select input').removeAttr("disabled");
            } else {
                $grid.find('.sotnik-grid-batch-select input').attr("disabled", "disabled");
            }
        },

        bindCheckAllBatchCheckboxes: function()
        {
            var that = this;

            $('.sotnik-grid-batch-column input').on('click', function() {
                var $grid = $(this).parents('.sotnik-grid-main'),
                    checkboxes = $grid.find('.sotnik-grid-batch-checkbox');

                if ($(this).prop('checked')) {
                    checkboxes.prop('checked', true);
                } else {
                    checkboxes.prop('checked', false);
                }

                that.checkAtListOneBatchCheckboxChecked($grid);
            });
        },

        bindCheckboxShiftCheck: function()
        {
            var that = this;
            var $chkboxes = $('.sotnik-grid-batch-checkbox');
            $chkboxes.on('click', function(e) {
                if(!that.lastChecked) {
                    that.lastChecked = this;
                    return;
                }

                if ($(this).parents('.sotnik-grid-main')[0] != $(that.lastChecked).parents('.sotnik-grid-main')[0]) {
                    that.lastChecked = this;
                    return;
                }

                if(e.shiftKey) {
                    var start = $chkboxes.index(this);
                    var end = $chkboxes.index(that.lastChecked);

                    $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', that.lastChecked.checked);

                }

                that.lastChecked = this;

            });
        },

        bindSubmitBatchActions: function ()
        {
            $('.sotnik-grid-batch-form').on('submit', function(e) {
                var selectedOption = $(this).find('.sotnik-grid-batch-select option:selected');

                if ($(this).data('wait-for-confirm') != undefined ||
                    parseInt($(this).data('wait-for-confirm')) == 1) {
                    return true;
                }

                if (parseInt(selectedOption.data('confirm')) == 1) {
                    e.preventDefault();

                    var text = selectedOption.data('confirm-text'),
                        buttonText = selectedOption.data('confirm-button-text'),
                        $modal = $(this).parents('.sotnik-grid-main').find('.sotnik-grid-modal'),
                        $modalSubmitButton = $modal.find('.modal-action-button'),
                        $form = $(this);

                    $modalSubmitButton.text(buttonText);
                    $modal.find('.modal-body').html(text);

                    $modalSubmitButton.on('click', function() {
                        $form.data('wait-for-confirm', 1);
                        $form.submit();
                    });

                    $modal.modal('show');
                }
            });
        },

        bindClickActionLink: function()
        {
            $('.sotnik-grid-action-link').on('click', function(e) {
                if (parseInt($(this).data('confirm')) == 1) {
                    e.preventDefault();

                    var text = $(this).data('confirm-text'),
                        buttonText = $(this).data('confirm-button-text'),
                        $modal = $(this).parents('.sotnik-grid-main').find('.sotnik-grid-modal'),
                        $modalSubmitButton = $modal.find('.modal-action-button'),
                        url = $(this).attr('href');

                    $modalSubmitButton.text(buttonText);
                    $modal.find('.modal-body').html(text);

                    $modalSubmitButton.on('click', function() {
                        location = url;
                    });

                    $modal.modal('show');
                }
            });
        },

        initDateTimePicker: function()
        {
            if (!$.fn.datetimepicker) {
                return;
            }

            $('input[data-date-time-start]').each(function(){
                var startInput = $(this),
                    endInput = startInput.next('input[data-date-time-end]'),
                    localeStart = startInput.data('locale') || 'en',
                    localeEnd = endInput.data('locale') || 'en';


                startInput.datetimepicker({
                    locale: localeStart
                });
                endInput.datetimepicker({
                    locale: localeEnd
                });
                startInput.on("dp.change",function (e) {
                    endInput.data("DateTimePicker").minDate(e.date);
                });
                endInput.on("dp.change",function (e) {
                    startInput.data("DateTimePicker").maxDate(e.date);
                });
            });

            $('input[data-date-time-input]').each(function(){
                var locale = $(this).data('locale') || 'en';
                $(this).datetimepicker({
                    locale: locale
                });
            });
        }
    }

    sotnikGrid.init();

})();

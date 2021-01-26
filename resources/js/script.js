import {Datatable} from 'uccello-datatable';

export class UrlExportManager
{
    constructor() {
        this.initDatatable();

        $('#exportModal .copy-url').each((index, el) => {
            this.initCopyUrlButton($(el));
        });

        $('#exportModal .delete-url').each((index, el) => {
            this.initDeleteUrlButton($(el));
        });

        this.initGenerateExportUrlClickListener();
    }

    /**
     * Init datatable
     */
    initDatatable() {
        if ($('table[data-filter-type="list"]').length == 0) {
            return
        }

        this.datatable = new Datatable();
        this.datatable.init($('table[data-filter-type="list"]'));
    }

    initCopyUrlButton(element) {
        $(element).on('click', event => {
            event.preventDefault();

            let inputId = element.parents('.export-url:first').find('.export-url-value').attr('id');
            this.copyUrl(inputId);
        });
    }

    initDeleteUrlButton(el) {
        let element = $(el);

        element.on('click', event => {
            event.preventDefault();

            let data = {
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            let url = element.attr('href');
            $.post(url, data).then(response => {
                if (response.success) {
                    element.parents('.export-url:first').remove();
                }
            })
        });
    }

    initGenerateExportUrlClickListener() {
        $('.generate-export-url').on('click', (event) => {
            event.preventDefault();

            let endpointUrl = $(event.currentTarget).attr('href');

            this.generateExportUrl(endpointUrl);
        })
    }

    generateExportUrl(endpointUrl) {
        let formData = this.getExportConfig();

        $.post(endpointUrl, formData).then(response => {
            let element = $(response.html_content);

            // Prepend the link
            $('#exportModal #export-urls-list').prepend(element);

            // Initialize copy url button
            this.initCopyUrlButton($('.copy-url', element));

            // Initialize delete url button
            this.initDeleteUrlButton($('.delete-url', element));

            // Initialize Materialize for this added content
            let customEvent = new CustomEvent('js.init.materialize', {
                detail: {
                    element: element
                }
            });
            dispatchEvent(customEvent);
        });
    }

    getExportConfig() {
        const table = $('table[data-filter-type="list"]')
        const modal = $('#exportModal')

        return {
            _token: $('meta[name="csrf-token"]').attr('content'),
            extension: $('#export_format', modal).val(),
            columns: this.getVisibleColumns(table),
            conditions: this.getSearchConditions(table),
            order: $(table).attr('data-order') ? JSON.parse($(table).attr('data-order')) : null,
            with_hidden_columns: $('#with_hidden_columns', modal).is(':checked') ? 1 : 0,
            with_id: $('#export_with_id', modal).is(':checked') ? 1 : 0,
            with_conditions: $('#export_keep_conditions', modal).is(':checked') ? 1 : 0,
            with_order: $('#export_keep_order', modal).is(':checked') ? 1 : 0,
            with_timestamps: $('#export_with_timestamps', modal).is(':checked') ? 1 : 0,
            with_descendants: $('#export_with_descendants', modal).val(),
        }
    }

    /**
     * Get datatable visible columns
     * @param {Datatable} table
     * @return {array}
     */
    getVisibleColumns(table) {
        let visibleColumns = [];

        $('th[data-field]', table).each((index, element) => {
            let fieldName = $(element).data('field')

            if ($(element).is(':visible')) {
                visibleColumns.push(fieldName)
            }
        })

        return visibleColumns
    }

    /**
     * Get search conditions
     * @param {Datatable} table
     * @return {Object}
     */
    getSearchConditions(table) {
        let conditions = { search: {}}

        $('th[data-column]', table).each((index, el) => {
            let fieldName = $(el).data('field')
            if (this.datatable.columns[fieldName].search) {
                conditions.search[fieldName] = this.datatable.columns[fieldName].search
            }
        })

        return conditions
    }

    /**
     * Copy URL to clipboard.
     *
     * @param {String} inputId
     */
    copyUrl(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        document.execCommand("copy");
      }
}

new UrlExportManager();

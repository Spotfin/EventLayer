/**
 * EventLayer Admin JavaScript
 * 
 * @package EventLayer
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Child Selectors Repeater
        var childSelectorIndex = $('#child-selectors-container .child-selector-row').length;

        // Add Child Selector
        $('#add-child-selector').on('click', function () {
            var newRow = $('<div class="child-selector-row" style="margin-bottom: 10px;">' +
                '<input type="text" name="child_selectors[]" value="" placeholder="Child selector (optional)" class="regular-text" /> ' +
                '<button type="button" class="button remove-child-selector">Remove</button>' +
                '</div>');
            $('#child-selectors-container').append(newRow);
        });

        // Remove Child Selector
        $(document).on('click', '.remove-child-selector', function () {
            var container = $('#child-selectors-container');
            if (container.find('.child-selector-row').length > 1) {
                $(this).closest('.child-selector-row').remove();
            } else {
                // Clear the input instead of removing the last row
                $(this).siblings('input').val('');
            }
        });

        // Parameters Repeater
        var parameterIndex = $('#parameters-tbody .parameter-row').length;

        // Add Parameter - clone server-rendered template to ensure options reflect filters
        $('#add-parameter').on('click', function () {
            var $tpl = $('#parameter-template-row').clone().removeAttr('id');
            $tpl.find('input, select').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace('__NAME__', 'parameters[' + parameterIndex + ']'));
                }
            });
            $('#parameters-tbody').append($tpl.show());
            parameterIndex++;
        });

        // Remove Parameter
        $(document).on('click', '.remove-parameter', function () {
            var tbody = $('#parameters-tbody');
            if (tbody.find('.parameter-row').length > 1) {
                $(this).closest('.parameter-row').remove();
            } else {
                // Clear the inputs instead of removing the last row
                $(this).closest('.parameter-row').find('input, select').val('');
                $(this).closest('.parameter-row').find('select').prop('selectedIndex', 0);
            }
        });

        // Update parameter indices when rows are removed
        function updateParameterIndices() {
            $('#parameters-tbody .parameter-row').each(function (index) {
                $(this).find('input, select').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/parameters\[\d+\]/, 'parameters[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        // Call updateParameterIndices when parameters are removed
        $(document).on('click', '.remove-parameter', function () {
            setTimeout(updateParameterIndices, 10);
        });

        // Toggle target selector field based on target type
        $(document).on('change', 'select[name*="[target_type]"]', function () {
            var targetSelector = $(this).closest('tr').find('input[name*="[target_selector]"]');
            var selectedType = $(this).val();

            if (selectedType === 'static') {
                targetSelector.attr('placeholder', 'Not used for static values');
                targetSelector.prop('disabled', true);
            } else if (selectedType === 'element_text') {
                targetSelector.attr('placeholder', 'CSS selector');
                targetSelector.prop('disabled', false);
            } else if (selectedType === 'element_attribute') {
                targetSelector.attr('placeholder', 'attribute name (e.g., data-value)');
                targetSelector.prop('disabled', false);
            } else if (selectedType === 'url_parameter') {
                targetSelector.attr('placeholder', 'URL parameter name');
                targetSelector.prop('disabled', false);
            }
        });

        // Initialize target selector fields on page load
        $('select[name*="[target_type]"]').each(function () {
            $(this).trigger('change');
        });

    });

})(jQuery);

(function($){
    $(document).ready(function(){
        var $btn = $('.wlbw-wc-btn');
        if (!$btn.length) return;

        var isVariableProduct = $('form.variations_form').length > 0;
        var requireVariation = typeof wlbwSettings !== 'undefined' && (wlbwSettings.requireVariation === true || wlbwSettings.requireVariation === '1');


        if (isVariableProduct && requireVariation) {
            $btn.addClass('disabled').prop('disabled', true);
        }

        $('form.variations_form').on('show_variation', function(event, variation){
            if (variation && variation.attributes && !hasEmptyAttributes()) {
                var selectedVariation = [];

                $('form.variations_form select').each(function () {
                    var $select = $(this);
                    var value = $select.val();

                    if (value) {
                        var label = $select.find('option:selected').text();
                        selectedVariation.push(label);
                    }
                });

                selectedVariation = selectedVariation.join(', ');

                var baseUrl = $btn.data('base-url');
                var message = $btn.data('message');
                var productName = $btn.data('product-name');

                var fullMessage = message + ' ' + productName;
                if (selectedVariation) {
                    fullMessage += ' (' + selectedVariation + ')';
                }

                $btn.attr('href', baseUrl + encodeURIComponent(fullMessage));
                $btn.removeClass('disabled').prop('disabled', false);
            }
        });

        $('form.variations_form').on('check_variations', function(){
            if (requireVariation) {
                $btn.addClass('disabled').prop('disabled', true);
            }
        });

        $('form.variations_form').on('reset_data', function () {
            var baseUrl = $btn.data('base-url');
            var message = $btn.data('message');
            var productName = $btn.data('product-name');

            if (requireVariation) {
                $btn
                    .attr('href', '#')
                    .addClass('disabled')
                    .prop('disabled', true);
            } else {
                $btn
                    .attr('href', baseUrl + encodeURIComponent(message + ' ' + productName))
                    .removeClass('disabled')
                    .prop('disabled', false);
            }
        });

        $('form.variations_form select[name^="attribute_"]').on('change', function(){
            if (hasEmptyAttributes()) {
                $btn.addClass('disabled').prop('disabled', true);
            }
        });

        function hasEmptyAttributes() {
            var empty = false;

            $('form.variations_form select[name^="attribute_"]').each(function(){
                if (!$(this).val()) {
                    empty = true;
                    return false;
                }
            });

            return empty;
        }

        $btn.on('click', function(e){
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
            }
        });

    });
})(jQuery);
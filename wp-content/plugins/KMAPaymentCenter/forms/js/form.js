(function($) {
    $(document).ready(function(){
        let whattopay = $('#what_to_pay'),
            payinvoice = $('#payinvoice'),
            recurringservice = $('#recurringservice'),
            serviceinput = $('#service_name');

        if (whattopay.val() !== '') {
            changeType(whattopay)
        }
        whattopay.change(function () {
            if (whattopay.val() !== '') {
                changeType(whattopay)
            }
        });

        function changeType(input) {
            if (input.val() === 'invoice') {
                payinvoice[0].style.display = 'flex';
                recurringservice[0].style.display = 'none';
            }
            if (input.val() === 'recurring-service') {
                payinvoice[0].style.display = 'none';
                recurringservice[0].style.display = 'flex';
            }
        }

        if (serviceinput.val() !== '') {
            setTermValues()
        }
        serviceinput.change(function () {
            if (serviceinput.val() !== '') {
                setTermValues()
            }
        });

        function setTermValues() {
            let source = $('#service_name')[0],
                key = source.value;

            let id = source.options[key].dataset.id,
                price = Number(source.options[key].dataset.price),
                term = Number(source.options[key].dataset.term),
                termtype = source.options[key].dataset.termType;

            $('#service_amount').val(price.toFixed(2));
            $('#service_term').val(term);
            $('#service_term_type').val(termtype);

            let service_term = $('#service_term_display');
            if (term === 1) {
                if (termtype === 'months') {
                    service_term.html('monthly')
                }
                if (termtype === 'days') {
                    service_term.html('daily')
                }
            } else if (term === 12) {
                if (termtype === 'months') {
                    service_term.html('annually')
                }
            } else {
                service_term.html('every ' + term + ' ' + termtype)
            }
        }
    });
}(window.jQuery));
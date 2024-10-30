/*
jQuery('#lypay_radio-option-credit').on('click', function (){
    console.log('index', this)
});

jQuery('#lypay_radio-option-billet').on('click', function (){
    console.log('index', this)
});

jQuery('#lypay_radio-option-pix').on('click', function (){
    console.log('index', this)
});*/

(function($ly) {

    function showCardInputs(){
        console.log($ly('input[name="lypay_finalPaymentMethod"]').val());

        $ly('input[name="lypay_finalPaymentMethod"]').change(function (){
            console.log(jQuery(this).val());

            if(jQuery(this).val() == 'credit_card'){
                jQuery('.lypay-credit_card').show();
                var options =  {
                    onKeyPress: function (cpf, ev, el, op) {
                        var masks = ['000.000.000-000', '00.000.000/0000-00'];
                        jQuery('input[name="lypay-credit_card__holder-cpf"]').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                    }
                }

                jQuery('input[name="lypay-credit_card__holder-cpf"]').mask('00.000.000/0000-00', options);
                jQuery('input[name="lypay-credit_card__number"]').mask('0000 0000 0000 0000');
                jQuery('input[name="lypay-credit_card--expiry"]').mask('00/0000');
                jQuery('input[name="lypay-credit_card--cvv"]').mask('0000');
            }else {
                jQuery('.lypay-credit_card').hide();
            }


        });
    }


    $ly(document).ready(function (){
        console.log('Carregou o main.js');
        jQuery('input[name="lypay_finalPaymentMethod"]').on('click',function (){
            console.log('carregou');
        });
        showCardInputs();
        $ly('#copy-code').click(function(){
            copy_billet_lytex();
        })
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }

        function copy_billet_lytex(){
            var copyText = document.getElementById("copy_ticket_lytex");
            copyToClipboard(copyText.value);
        }



                //Verifica Select CPF/CNPJ 
                jQuery('#lytex_cpfcnpj').mask('999.999.999-99');
                jQuery('#billing_phone').mask('(99)9 9999-9999');
                jQuery('#lytex_select_cpf-cnpj').change(function(event){
                    var doc = event.currentTarget.value;
                    if(doc == "pf"){
                        document.querySelector('#lytex_cpfcnpj_field label').innerHTML="CPF";
                        jQuery('#lytex_cpfcnpj_field label').append('<abbr class="required" title="required">*</abbr>');
                        jQuery('#lytex_cpfcnpj').mask('999.999.999-99');
                    }
                    if(doc == "pj"){
                        document.querySelector('#lytex_cpfcnpj_field label').innerHTML="CNPJ";
                        jQuery('#lytex_cpfcnpj_field label').append('<abbr class="required" title="required">*</abbr>');
                        jQuery('#lytex_cpfcnpj').mask('99.999.999/9999-99');
                    }
                });


    });

    $ly(window).on('load', function (){

        $ly('input[name="lypay_finalPaymentMethod"]').each(function (){
            jQuery(this).click(function (){
                console.log('teste');
            });
        });
        $ly('input[name="billing_email"]').on('click',function (){
            console.log('teste');
        });



    })



})(jQuery);

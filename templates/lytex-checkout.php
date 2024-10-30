<?php
    if (!defined('ABSPATH')) {
        exit;
    }

        if ( $billet_option == 'no' && $pix_option == 'no' && $credit_card_option == 'no') :
            echo esc_html( __( 'There is no payment option selected.', 'text_domain' ) );
        else :
?>
<div class="lypay_checkout-pannel" id="lypay_checkout-pannel">
    <div class="lypay_pannel-options">
        <div class="lypay_radio-option" id="lypay_radio-option-pix">
            <input type="hidden" name="lypay_finalPaymentMethod" id="radio_lypay_pix" value="pix">
            <div class="lypay_pannel-option__text">
                <label for="radio_lypay_pix">Pix</label>
            </div>
        </div>
    </div>
</div>

<?php  endif; ?>
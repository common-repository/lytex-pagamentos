<?php
if (!defined('ABSPATH')) {
    exit;
}
 ?>
    <div class="lypay_checkout-pannel" id="lypay_checkout-pannel">
        <div class="lypay_pannel-options">


                <div class="lypay-credit_card">
                    <label for="lypay-credit_card__holder-name">Nome do Portador</label>
                    <input type="text" name="lypay-credit_card__holder-name" class="lypay-credit_card__holder-name" placeholder="JOSE O SILVA">
                    <label for="lypay-credit_card__holder-cpf">CPF/CNPJ do Portador</label>
                    <input type="text" name="lypay-credit_card__holder-cpf" class="lypay-credit_card__holder-cpf">
                    <label for="lypay-credit_card__number">Número do Cartão</label>
                    <input type="text" name="lypay-credit_card__number" class="lypay-credit_card__number">
                    <label for="lypay-credit_card--expiry">Vencimento</label>
                    <input type="text" name="lypay-credit_card--expiry" class="lypay-credit_card--expiry">
                    <label for="lypay-credit_card--cvv">Código de Segurança</label>
                    <input type="text" name="lypay-credit_card--cvv" class="lypay-credit_card--cvv">
                    <label for="lypay-credit_card--parcels">Selecione o número de parcelas</label>

                    <?php $num_instalments = ($this->max_parcels <= $num_instalments) ? $this->max_parcels : $num_instalments ?>
                    <select name="lypay-credit_card--parcels" id="lypay-credit_card--parcels" class="lypay-credit_card--parcels">
                        <option value="">Número de parcelas</option>
                        <?php for ($i = 1; $i <= $num_instalments; $i++): ?>
                            <option value="<?php echo esc_html($i) ;?>"><?php echo esc_html($i . "x de " . number_format(($card_total / $i), 2, ",", ".") )?></option>
                        <?php endfor;?>
                    </select>
                </div>
        </div>
    </div>

    <script>

        jQuery(document).ready(function (){

            var options =  {
                onKeyPress: function (cpf, ev, el, op) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    jQuery('input[name="lypay-credit_card__holder-cpf"').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                }
            }
            jQuery('.lypay-credit_card').show();
            jQuery('input[name="lypay-credit_card__holder-cpf"]').mask('00.000.000/0000-00', options);
            jQuery('input[name="lypay-credit_card__number"]').mask('0000 0000 0000 0000');
            jQuery('input[name="lypay-credit_card--expiry"]').mask('00/0000');
            jQuery('input[name="lypay-credit_card--cvv"]').mask('0000');

        });


    </script>

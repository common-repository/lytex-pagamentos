<?php

if (!defined('ABSPATH')) {
	exit;
}

if ( $billet_option == 'no' && $pix_option == 'no' && $credit_card_option == 'no') :

	// $credit_option == "no" &&

	echo "Não existe nenhuma opção de pagamento selecionada.";

else :

	?>

	<script>

        jQuery(document).ready(function (){

            jQuery('#lypay_number_credit').parent().hide();

            jQuery('.lypay_form-group-expire').parent().hide();

            jQuery('#lypay_cvc_credit').parent().hide();

            if(!jQuery('#billing_persontype')[0]){
                jQuery('#lypay_type').parent().show()
            } else {
                jQuery('#lypay_type').parent().hide()
            }

            if(jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val() + ' ' + jQuery('#billing_last_name').val())
            } else if (jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() === '') {
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val())
            } else if (jQuery('#billing_first_name').val() === '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_last_name').val())
            }

            jQuery('#lypay_email').val(jQuery('#billing_email').val())

            jQuery('#lypay_zip_code').val(jQuery('#billing_postcode').val())

            jQuery('#lypay_address').val(jQuery('#billing_address_1').val())

            jQuery('#lypay_address_complement').val(jQuery('#billing_address_2').val())

            jQuery('#lypay_city').val(jQuery('#billing_city').val())

            jQuery('#lypay_cpf').val(jQuery('#billing_cpf').val())

            jQuery('#lypay_phone').val(jQuery('#billing_phone').val())

            jQuery('#lypay_zone').val(jQuery('#billing_neighborhood').val())

            jQuery('#lypay_number').val(jQuery('#billing_number').val())

            if(jQuery('#lypay_type').find('option[value='+jQuery('#billing_persontype').val()+']').val()){
                jQuery('#lypay_type').val(jQuery('#billing_persontype').val())
            }

            if(jQuery('#lypay_state').find('option[value='+jQuery('#billing_state').val()+']').val()){
                jQuery('#lypay_state').val(jQuery('#billing_state').val())
            }

        })

        jQuery('#lypay_radio-option-credit').on('click', function (){
            selectRadioLytex('credit');
        });

        jQuery('#lypay_radio-option-billet').on('click', function (){
            selectRadioLytex('billet');
        });

        jQuery('#lypay_radio-option-pix').on('click', function (){
            selectRadioLytex('pix');
        });

        function selectRadioLytex(btnClicked){

            let option = jQuery('#lypay_radio-option-'+btnClicked);

            if(!option.hasClass('lypay_selected')){

                jQuery('.lypay_selected').removeClass('lypay_selected');

                option.addClass('lypay_selected');

                option.find('input[type=radio]').click();

                if( option.attr('id') === 'lypay_radio-option-credit' ){

                    jQuery('#lypay_number_credit').parent().show();

                    jQuery('.lypay_form-group-expire').parent().show();

                    jQuery('#lypay_cvc_credit').parent().show();

                } else {

                    jQuery('#lypay_number_credit').parent().hide();

                    jQuery('.lypay_form-group-expire').parent().hide();

                    jQuery('#lypay_cvc_credit').parent().hide();

                }

            } else {

                return false;

            }

        }

        jQuery('#billing_first_name').on('blur', function (){
            if(jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val() + ' ' + jQuery('#billing_last_name').val())
            } else if (jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() === '') {
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val())
            } else if (jQuery('#billing_first_name').val() === '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_last_name').val())
            }
        })

        jQuery('#billing_last_name').on('blur', function () {
            if(jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val() + ' ' + jQuery('#billing_last_name').val())
            } else if (jQuery('#billing_first_name').val() !== '' && jQuery('#billing_last_name').val() === '') {
                jQuery('#lypay_name').val(jQuery('#billing_first_name').val())
            } else if (jQuery('#billing_first_name').val() === '' && jQuery('#billing_last_name').val() !== ''){
                jQuery('#lypay_name').val(jQuery('#billing_last_name').val())
            }
        })

        jQuery('#billing_email').on('blur',function (){
            jQuery('#lypay_email').val(jQuery('#billing_email').val())
        })

        jQuery('#billing_postcode').on('blur',function (){
            jQuery('#lypay_zip_code').val(jQuery('#billing_postcode').val())
        })

        jQuery('#billing_address_1').on('blur',function (){
            jQuery('#lypay_address').val(jQuery('#billing_address_1').val())
        })

        jQuery('#billing_address_2').on('blur',function (){
            jQuery('#lypay_address_complement').val(jQuery('#billing_address_2').val())
        })

        jQuery('#billing_city').on('blur',function (){
            jQuery('#lypay_city').val(jQuery('#billing_city').val())
        })

        jQuery('#billing_cpf').on('blur',function (){
            jQuery('#lypay_cpf').val(jQuery('#billing_cpf').val())
        })

        jQuery('#billing_phone').on('blur',function (){
            jQuery('#lypay_phone').val(jQuery('#billing_phone').val())
        })

        jQuery('#billing_neighborhood').on('blur',function (){
            jQuery('#lypay_zone').val(jQuery('#billing_neighborhood').val())
        })

        jQuery('#billing_number').on('blur',function (){
            jQuery('#lypay_number').val(jQuery('#billing_number').val())
        })

        jQuery('#billing_persontype').on('change', function (){

            if(jQuery('#lypay_type').find('option[value='+jQuery('#billing_persontype').val()+']').val()){
                jQuery('#lypay_type').val(jQuery('#billing_persontype').val())
            }

        })

        jQuery('#billing_state').on('change', function (){

            if(jQuery('#lypay_state').find('option[value='+jQuery('#billing_state').val()+']').val()){
                jQuery('#lypay_state').val(jQuery('#billing_state').val())
            }

        })

	</script>

	<div class="lypay_checkout-pannel" id="lypay_checkout-pannel" style="display: none">

		<div class="lypay_pannel-options">
			<?php //if($credit_option == "yes"): ?>

			<?php  //endif;
			if($billet_option == "yes"):?>

				<div class="lypay_radio-option lypay_selected" id="lypay_radio-option-billet">
					<input type="radio" name="lypay_finalPaymentMethod" id="radio_lypay_billet" value="billet" checked>
					<div class="lypay_pannel-option__text">
						<label for="radio_lypay_billet">Boleto</label>
					</div>
				</div>

			<?php endif;
			if($pix_option == "yes"):?>

				<div class="lypay_radio-option" id="lypay_radio-option-pix">
					<input type="radio" name="lypay_finalPaymentMethod" id="radio_lypay_pix" value="pix">
					<div class="lypay_pannel-option__text">
						<label for="radio_lypay_pix">Pix</label>
					</div>
				</div>

			<?php endif; ?>
            <?php if($credit_card_option == "yes"):?>

				<div class="lypay_radio-option" id="lypay_radio-option-credit_card">
					<input type="radio" name="lypay_finalPaymentMethod" id="radio_lypay_credit_card" value="credit_card">
					<div class="lypay_pannel-option__text">
						<label for="radio_lypay_credit_card">Cartão de Crédito</label>
					</div>
				</div>

			<?php endif; ?>
		</div>

		<div class="lypay_form-wrapper lypay_form-billet">
			<div class="lypay_field-wrapper">
				<label for="lypay_name">Nome Completo</label>
				<input type="text" name="lypay_name" id="lypay_name">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_type">Tipo de Pessoa</label>
				<select name="lypay_type" id="lypay_type">
					<option value="1">Pessoa Física</option>
					<option value="2">Pessoa Jurídica</option>
				</select>
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_cpf">CPF/CNPJ</label>
				<input type="text" name="lypay_cpf" id="lypay_cpf">
			</div>

			<div class="lypay_field-wrapper">
				<label for="lypay_phone">Número de Telefone</label>
				<input type="text" name="lypay_phone" id="lypay_phone">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_email">E-mail</label>
				<input type="email" name="lypay_email" id="lypay_email">
			</div>
			<div class="lypay_form-group">
				<div class="lypay_field-wrapper lypay_field-wrapper--half">
					<label for="lypay_zip_code">CEP</label>
					<input type="text" name="lypay_zip_code" id="lypay_zip_code">
				</div>
				<div class="lypay_field-wrapper lypay_field-wrapper--half">
					<label for="lypay_state">Estado</label>
					<select name="lypay_state" id="lypay_state">
						<option value="disabled_op" selected disabled>Escolha uma opção...</option>
						<option value="AC">Acre</option>
						<option value="AL">Alagoas</option>
						<option value="AP">Amapá</option>
						<option value="AM">Amazonas</option>
						<option value="BA">Bahia</option>
						<option value="CE">Ceará</option>
						<option value="DF">Distrito Federal</option>
						<option value="ES">Espírito Santo</option>
						<option value="GO">Goiás</option>
						<option value="MA">Maranhão</option>
						<option value="MT">Mato Grosso</option>
						<option value="MS">Mato Grosso do Sul</option>
						<option value="MG">Minas Gerais</option>
						<option value="PA">Pará</option>
						<option value="PB">Paraíba</option>
						<option value="PR">Paraná</option>
						<option value="PE">Pernambuco</option>
						<option value="PI">Piauí</option>
						<option value="RJ">Rio de Janeiro</option>
						<option value="RN">Rio Grande do Norte</option>
						<option value="RS">Rio Grande do Sul</option>
						<option value="RO">Rondônia</option>
						<option value="RR">Roraima</option>
						<option value="SC">Santa Catarina</option>
						<option value="SP">São Paulo</option>
						<option value="SE">Sergipe</option>
						<option value="TO">Tocantins</option>
					</select>
				</div>
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_address">Endereço</label>
				<input type="text" name="lypay_address" id="lypay_address">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_number">Número</label>
				<input type="text" name="lypay_number" id="lypay_number">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_address_complement">Complemento</label>
				<input type="text" name="lypay_address_complement" id="lypay_address_complement">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_city">Cidade</label>
				<input type="text" name="lypay_city" id="lypay_city">
			</div>
			<div class="lypay_field-wrapper">
				<label for="lypay_zone">Bairro</label>
				<input type="text" name="lypay_zone" id="lypay_zone">
			</div>
		</div>

	</div>

<?php

endif;

?>

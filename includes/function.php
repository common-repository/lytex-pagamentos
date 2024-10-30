<?php
add_action( 'wp_ajax_lytex_ajax_to_issue_duplicate_ticket', 'lytex_ajax_to_issue_duplicate_ticket');
function lytex_ajax_to_issue_duplicate_ticket(){
    include_once LYTEX_MAIN_PATH . '/api/class-api-lytex.php';
    $api = new Lytex_pagamentos_API();
    ob_start();
    $date2via =     sanitize_text_field( $_REQUEST['date2via'] ) ;
    $id_invoices =  sanitize_text_field( $_REQUEST['invoices_id'] );
    $id_order =     sanitize_text_field( $_REQUEST['id_order'] ); 
    $PaymentMethod =  get_post_meta($id_order, 'PaymentMethod', true);
    $result = $api->segunda_via($id_invoices, $date2via);
    if($result->message){
        echo esc_html($result->message);
    }else {
        if($PaymentMethod == "billet"){
            update_post_meta($id_order, 'barcode', $result->paymentMethods->boleto->barcode);
        }elseif($PaymentMethod == "pix"){
            update_post_meta($id_order, 'CodePix', $result->paymentMethods->pix->qrcode);
        }
        include_once LYTEX_MAIN_PATH . '/templates/order-received.php';
    }
    $html = ob_get_clean();
    wp_send_json($html);
}


add_filter( 'woocommerce_checkout_fields' , 'lytex_checkout_fields' );
function lytex_checkout_fields( $fields ) {
    if ( ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) :
        $fields['billing']['billing_first_lytex_select_cpf'] = array(
            'label'        => __('Select Person'),
            'type'         => 'select',
            'id'           => 'lytex_select_cpf-cnpj',
            'required'     => true,
            'options'      => array(
                'pf'=> __('Individuals'),
                'pj'=> __('Legal Person')
            ),
            'priority'     => 1,
        );
        $fields['billing']['billing_first_lytex_cpf'] = array(
            'label'        => __('CPF'),
            'type'         => 'text',
            'id'           => 'lytex_cpfcnpj',
            'required'     => true,
            'priority'     => 1,
        );

        $fields['billing']['billing_lytex_number_address'] = array(
            'label'        => 'Número',
            'type'         => 'text',
            'id'           => 'lytex_number_address',
            'required'     => true,
            'priority'     => 61,
        );
        $fields['billing']['billing_lytex_bairro_address'] = array(
            'label'        => 'Bairro',
            'type'         => 'text',
            'id'           => 'lytex_Bairro_address',
            'required'     => true,
            'priority'     => 60,
        );
    endif;
    //unset($fields['billing']['billing_last_name']); // Sobrenome
    $fields['billing']['billing_company']['required']= false;
    $fields['billing']['billing_country']['required']= false;
 return $fields;
}



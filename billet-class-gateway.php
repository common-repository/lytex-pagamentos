<?php
class Billet_Lytex_Gateway extends WC_Payment_Gateway{
    public function __construct() {

        $this->lytex_include();
        $this->id = 'lytex_billet';
        $this->has_fields = true;
        $this->method_title = 'Lytex Pagamentos - Boletos';
        $this->method_description = 'Receba pagamentos via Boleto Bancário';
        $this->supports = array(
            'products'
        );

        // Método com todos os campos de opções
        $this->init_form_fields();
        $this->includes();


        // Carregue as configurações.
        $this->init_settings();
        $this->title 			= $this->get_option( 'title' );
        $this->description 		= $this->get_option( 'description' );
        $this->enabled 			= $this->get_option( 'enabled' );
        $this->sandbox 			= $this->get_option( 'sandbox' );
        $this->Client_ID 		= $this->get_option( 'Client_ID' );
        $this->Client_Secret 	= $this->get_option( 'Client_Secret' );
        $this->Callback_Secret  = $this->get_option( 'Callback_Secret' );
        $this->recipientId 		= $this->get_option('recipient_Id');
        $this->duedate          = $this->get_option( 'duedate' );

        // This action hook saves the settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action('woocommerce_thankyou_lytex_billet', array($this, 'thankyou_page_lytex'));
        add_action('wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
        add_action('woocommerce_api_' . $this->id , array($this, 'webhook'));
        add_action('woocommerce_account_view-order_endpoint', array($this, 'woocommerce_account_view_order_lytex'), 1 );
        add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'lytex_issue_duplicate_ticket'), 1);
        $this->_api_service = new ApiService_Lytex_Pagamentos();


    }
    public function lytex_notice(){
        wc_print_notice( __( 'apenas um teste', 'woocommerce' ), 'error' );
    }

    /**
     * Essa função deve receber um parametro POST com os dados  da ordem de compra, esta função e responsável por
     * Inserir no painel do administrador o botão para gerar a segunda via do boleto ou do pix, lembrando que não sera criada
     * uma nava fatura ou um novo pedido somente alteramos a data de vencimento da fatura atual
     * @param $post
     * @return void
     */
    public function lytex_issue_duplicate_ticket($post){
        global $theorder;
        if ( ! is_object( $theorder ) ) {
            $theorder = wc_get_order( $post->ID );
        }
        $order = $theorder;
        //verifica se o metodo de pagamento e da Lytex
        if($order->data['payment_method'] == "lytex" && $order->data['status'] == "on-hold"):
            $invoices_id = get_post_meta($order->data['id'], 'invoices_id', true);
            $api = new Lytex_pagamentos_API();
            ?>
            <div id='duplicate_ticket_lytex' type='submit' class='button save_order button-primary'> Emitir 2 Via </div>
            <?PHP
            include_once dirname( __FILE__ ) . '/templates/views/modal_duplicate_ticket.php';
        endif;
    }

    /**
     * Alterações a area do cliente em /my-account, dentro de Ordens, onde os cliente da loja conde ver
     * seus produtos comprados, esta função verifica se o status do pedido se o status o mesmo for  on-hold
     * então a função imprime na tela o código do boleto ou pix depende do método de pagamento escolhido pelo cliente
     * na hora da compra, caso a fatura ja esteja paga não sera exibido nada
     * @return void
     */
    public function woocommerce_account_view_order_lytex(){
        //get id order
        global $wp;
        if(is_wc_endpoint_url( 'order-received' )) {
            $order_id  = wc_clean( $wp->query_vars['order-received'] );
        } elseif(is_view_order_page()) {
            $order_id = wc_clean( $wp->query_vars['view-order'] );
        }
        $order = wc_get_order( $order_id );
        //check order status
        if($order->data['status'] == "on-hold"):
            $method = get_post_meta($order_id, 'PaymentMethod', true);
            echo wp_kses_normalize_entities($this->lytex_thankyou_page($order_id, $method), $context = 'html');
        endif;
    }

    /**
     * Recebe um hook da plataforma da Lytex Pagamentos quando tiver alguma alteração  na fatura,
     * em seguida verificamos  essas informações e validamos os dados recebidos para que possamos
     * atualizar o status do pedido corretamente
     * @return void
     */
    public function webhook() {
        @ob_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        $api = new Lytex_pagamentos_API();
        $data = json_decode(file_get_contents('php://input'), true);
        $checks = $api->validate_hook_data( $data );
        if($checks){
            header( 'HTTP/1.1 200 OK' );
            $this->change_status($checks);
        }else{
            wp_die( esc_html__( 'Lytex Request Unauthorized' ), array( 'response' => 401 ) );
        }
    }

    /**
     * Função altera o status do pedido de acordo com o que e recebido da função anterior, webhook(). Deve ser passado
     * por parâmetro um objeto com o status do da fatura
     * @param $checks
     * @return void
     */
    private function change_status($checks){
        $order = wc_get_order($checks['id']);
        switch ($checks['status']){
            case "paid":
                $order->update_status('processing', __('Paid'));
                $order->payment_complete();
                break;
            case "canceled":
                $order->update_status('cancelled', __('cancelled'));
                break;
        }
    }

    /**
     * Exibe Informações na pagina de thankyou page
     * @param $order_id
     * @return void
     */
    public function thankyou_page_lytex($order_id){
        echo wp_kses_normalize_entities($this->lytex_thankyou_page($order_id), $context = 'html');
    }

    /**
     * Monta a estrutura da pagina de thankyou page
     * @param $order_id
     * @return false|string
     */
    protected function lytex_thankyou_page($order_id, $my_account = false){
        $PaymentMethod = sanitize_text_field($_REQUEST['PaymentMethod']) != null ? sanitize_text_field($_REQUEST['PaymentMethod']) : $my_account ;
        $this->lytex_include();
        $account = $my_account;
        $order = wc_get_order($order_id);
        ob_start();
        include_once dirname( __FILE__ ) . '/templates/order-received-billet.php';
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Faz toda a inclusão da API
     * @return void
     */
    public function includes(){
        include_once dirname( __FILE__ ) . '/api/class-api-lytex.php';
        include_once dirname( __FILE__ ) . '/api/api_service.php';
        include_once dirname( __FILE__ ) . '/includes/libs/LytexValidate.php';
    }

    /**
     * Faz toda a inclusão de folhas de style e JS para as configurações do dashboard
     * @return void
     */
    public function lytex_include(){
        wp_enqueue_script( 'lytex-checkout-script', plugins_url('assets/js/checkout/main.js', __FILE__));
        //wp_enqueue_script( 'lytex-checkout-script-blocks', plugins_url('assets/js/checkout/blocks.js', __FILE__));
        wp_enqueue_style( 'lytex-checkout-style', plugins_url('assets/css/style-checkout-lytex.css', __FILE__));
    }

    /**
     * Cria os formulario no dashboard
     * @return void
     */
    public function init_form_fields(){
        $this->form_fields = array(
            'enabled' => array(
                'title'       => 'Enable/Disable',
                'label'       => 'Habilitar Lytex Pagamentos',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'Titulo do metodo de pagamento checkout.',
                'default'     => 'Boleto',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'Descrição da tela de checkout.',
                'default'     => 'Pague pelo cartão de credito atravez do Lytex Pagamentos',
            ),
            'sandbox' => array(
                'title'       => 'Sandbox',
                'label'       => 'Modo Sandbox',
                'type'        => 'checkbox',
                'description' => 'Habilitar o modo de teste do Lytex Pagamentos',
                'default'     => 'yes',
                'desc_tip'    => true,
            ),
            'duedate' => array(
                'title'       => 'Prazo de validade',
                'label'       => 'Prazo de validade',
                'type'        => 'text',
                'description' => 'Vencimento da fatura Ex: 5 dias',
                'default'     => '5',
            ),
            'Client_ID' => array(
                'title'       => 'Client ID',
                'type'        => 'text'
            ),
            'Client_Secret' => array(
                'title'       => 'Client Secret',
                'type'        => 'text',
            )
        );
    }

    /**
     * Você precisará dele se quiser seu formulário de cartão de crédito personalizado
     * @return void
     */
    public function payment_scripts() {
        wp_enqueue_script( 'lytex-checkout-script', plugins_url('assets/js/checkout/main.js', __FILE__), '' ,rand(1, 1000), true);
    }

    /**
     * Função retorna o formato do preço
     * @param $value
     * @return string
     */
    public function ly_price_format($value){
        $value = number_format($value, 2, "", "");
        return $value;
    }

    /**
     * Criar a objeto com todos os produtos do carrinho, taxas e frete
     * @param $order
     * @return array
     */
    private function ly_get_products($order){
        $products = array();
        foreach ( $order->get_items() as $item_id => $item ) {
            $products[] = [
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'value' => (int)$this->ly_price_format($item['line_subtotal'] / $item['qty'])
            ];
        }
        if($order->get_total_tax() > 0){

            foreach ( $order->get_taxes() as $tax ) {

                $products[] = [
                    'name' => $tax->get_label(),
                    'quantity' => 1,
                    'value' =>  (int)$this->ly_price_format($tax->get_tax_total())
                ];

            }

        }
        if($order->get_shipping_total() > 0){

            $products[] = array(
                'name' => 'Frete',
                'quantity' => 1,
                'value' => (int)$this->ly_price_format($order->get_shipping_total())
            );

        }
        if($order->get_shipping_tax() > 0){

            $products[] = array(
                'name' => 'Taxa sobre o Frete',
                'quantity' => 1,
                'value' => (int)$this->ly_price_format($order->get_shipping_tax())
            );

        }
        return $products;
    }

    /**
     * Processando o pagamento do cliente
     * @param $order_id
     * @return string[]|void
     */
    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        $products = $this->ly_get_products($order);
        $api = new Lytex_pagamentos_API();
        $result = $api->post_create_invoice($_POST, $products, $order_id);
        if(! $result){
            wc_add_notice('Não foi possível gerar a solicitaçào de pagamento. Aguarde alguns minutos antes de tentar novamente', 'error');
        }
        elseif($result->error->details[0]->message == true){
            wc_add_notice($result->error->details[0]->message, 'error');
        }
        else{
            //verifica se e boleto
            $PaymentMethodLytex = sanitize_text_field($_POST['lypay_finalPaymentMethod']);
            if($PaymentMethodLytex == "billet"){
                add_post_meta(intval($order_id), 'digitableLine', $result->paymentMethods->boleto->digitableLine, true);
                add_post_meta(intval($order_id), 'barcode', $result->paymentMethods->boleto->barcode, true);
                add_post_meta(intval($order_id), 'invoices_id', $result->_id, true);
                add_post_meta(intval($order_id), 'PaymentMethod', 'billet', true);
                update_post_meta(intval($order_id), '_payment_method_title', sanitize_text_field(__('Billet Banking - Lytex Pagamentos')));
                $order->update_status( 'on-hold', __( 'Awaiting online payment', 'wc-gateway-lytex' ) );
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order ) . '&PaymentMethod='. sanitize_text_field($_POST['lypay_finalPaymentMethod'])
                );
                //verifica se e boleto
            }else{
                return array(
                    'result'   => 'fail',
                    'redirect' => '',
                );

            }
        }

    }


    /**
     * Criando os campos do formulario Lytex
     * @return void
     */
    public function payment_fields() {
//        wp_register_script('jquery-wp', '/wp-includes/js/jquery/jquery.js', false);
//        wp_enqueue_script('jquery-wp');
        wp_enqueue_script('jquery-mask', plugins_url('/lytex-pagamentos/assets/js/checkout/jquery.mask.js', plugin_dir_path(__FILE__)), array('jquery-core-js'), '', true);
        wp_enqueue_script( 'lytex-checkout-script', plugins_url('assets/js/checkout/main.js', __FILE__));
        //$this->lytex_notice();
        // ok, let's display some descrip_tion before the payment form
        if ( $this->description ) {
            // you can instructions for test mode, I mean test card numbers etc.
            if ( $this->sandbox == "yes" ) {
                $this->description = __('Sandbox mode is activated, invoices created must not be paid.');
                $this->description  = trim( $this->description );
            }

            echo esc_html($this->description);
        }

        $card_total = $this->get_order_total();
        $num_instalments = intdiv( $card_total,5);
        include plugin_dir_path(dirname(__FILE__)) . 'lytex-pagamentos/templates/billet-checkout.php';


    }
}
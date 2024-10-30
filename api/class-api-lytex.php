<?php
// Exit if runs outside WP.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Lytex_pagamentos_API extends WC_lytex_Gateway {


    /**
     * Informa a URL correta da API
     * @param $metod
     * @return string
     */
    public function get_base_url($metod) {
        if($this->sandbox == "no"){
            return 'https://'.$metod.'-pay.lytex.com.br';
        }else{
            return 'https://sandbox-'.$metod.'-pay.lytex.com.br';
        }
	}
    
    /**
     * Criar o token de acesso a API Lytex Pagamentos
     * @return mixed
     */
    public function post_access_token() {
        $path = '/v1/oauth/obtain_token';
        $body =  array(
            "grantType"     => "clientCredentials",
            "clientId"      => $this->Client_ID,
            "clientSecret"  => $this->Client_Secret,
            "scopes"        =>[
                "client",
                "invoice",
                "paymentLink",
                "product"
            ]
        );
        $result =  $this->_api_service->post($this->get_base_url('auth'), $path, $body);

        return $result->data->accessToken;

    }

    /**
     * Remove da string todos os careceres especiais, 
     * basta passar como parâmetro a string que deseja remover os careceres
     * @param $str
     * @return array|string|string[]|null
     */
    function limpar_texto($str){
        return preg_replace("/[^0-9]/", "", $str);
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $id_invoices
     * @param [type] $date
     * @return void
     */
    public function segunda_via($id_invoices, $date){
        $path = '/v1/invoices/'.$id_invoices;
        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $datetime = $date ."T23:59:00.000Z";
        $body = array(
            "dueDate"=> $datetime
        );
        $result =  $this->_api_service->put($this->get_base_url('api'), $path, $body, $header);
        return $result->data;
    }
    

    /**
     * Gerar uma fatura para o cliente
     * @param $post
     * @param $itens
     * @return mixed
     */
    public function post_create_invoice($post = null, $itens = null, $referenceId = null){

        $path = '/v2/invoices';

        if($post['billing_first_lytex_cpf']){

            $_cpf = $this->limpar_texto($post['billing_first_lytex_cpf']);
            $_typePersona = $post['billing_first_lytex_select_cpf'];
            $_number_address = $post['billing_lytex_number_address'];
            $_bairro_address = $post['billing_lytex_bairro_address'];

        }elseif($post['billing_persontype']){

            $_number_address = $post['billing_number'];
            $_bairro_address = $post['billing_neighborhood'];

            $persona = $post['billing_persontype'];
            switch ($persona) {
                case '1':
                        $_typePersona = "pf";
                        $_cpf = $this->limpar_texto($post['billing_cpf']);
                    break;
                
                case '2':
                        $_typePersona = "pj";
                        $_cpf = $this->limpar_texto($post['billing_cnpj']);
                    break;
            }
        }
        
        $_cellphone = $this->limpar_texto($post['billing_phone']);

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $vencimento = 'P'.$this->duedate.'D';
        $datetime = new DateTime();
        $datetime->add(new DateInterval($vencimento));
        $order_id = "$referenceId";

        $body = array(
            "referenceId" => $order_id,
            "client"=> [
                "treatmentPronoun"=> "you",
                "name"=> $post['billing_first_name'] . " " . $post['billing_last_name'], //Name last Name OK
                "type"=> $_typePersona, //type document OK
                "cpfCnpj"=> $_cpf, // CPF/CNPJ OK
                "email"=> $post['billing_email'], //E-mail OK
                "cellphone"=> $_cellphone, //CellPhone OK
                "address"=> [
                    "number"=> $_number_address,
                    "zip"=> str_replace('-','', $post['billing_postcode']),
                    "city"=> $post['billing_city'],
                    "street"=> $post['billing_address_1'],
                    "state"=> $post['billing_state'],
                    "zone"=> $_bairro_address
                ]
            ],
            "items"=> $itens,
            "dueDate"=> $datetime->format(DateTime::ATOM),
            "cancelOverdueDays" => 1,
            "paymentMethods"=> [
              "pix"=> [
                "enable"=> $post['payment_method'] == "lytex" ? true: false
              ],
              "boleto"=> [
                "enable"=> $post['payment_method'] == "lytex_billet" ? true: false
              ],
              "creditCard"=> [
                "enable"=> $post['payment_method'] == "lytex_card" ? true: false,
                "maxParcels"=> 1,
                "isRatesToPayer" => false
              ]
            ],


        );
        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        return $result->data;
    }

    /**
     * validar os dados envido via hook da API
     * @param $data
     * @return mixed
     */
    public function validate_hook_data($data){
        //pegando a ID da fatura
        $invoiceId = $data['data']['invoiceId'];
        $path = '/v1/invoices/'.$invoiceId;
        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $result =  $this->_api_service->get($this->get_base_url('api'), $path, $header);
        $response = array(
            'id'=> $result->data->referenceId,
            'status'=> $result->data->status
        );
        return $response;
    }

    /**
     * Gerar o token do cartão de crétido para realizar o pagamento da fatura
     * @param $card_data
     * @return mixed
     */

    public function create_card_token($card_data){
        $path = '/v2/subscriptions/card_token';

        $body = array(
            "cpfCnpj" => $this->limpar_texto($card_data['cpfCnpj']),
            "number" => $this->limpar_texto($card_data['number']),
            "holder" =>$card_data['holder'],
            "expiry" => $this->limpar_texto($card_data['expiry']),
            "cvc" => $this->limpar_texto($card_data['cvc']),
            "_clientId" => (string) $card_data['_clientId']
        );

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );
        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        if ($result->status >= 200 && $result->status <= 210){
            return array(
                'error' => false,
                'token' => $result->data->_id
            );
        }else{
            return array(
                'error' => true,
                'message' => 'Erro nos dados do cartão: '.$result->data->error->details[0]->context->message
            );
        }
    }

    /**
     * Realizar o pagamento da fatura utilizando o token do cartão gerado
     * @param $invoice_id
     * @param $token
     * @return mixed
     */

    public function payInvoiceCardToken($invoice_id, $token){
        $path = '/v2/subscriptions/managed/pay';

        $body = array(
            "_invoiceId" => $invoice_id,
            "_cardTokenId" => $token
        );

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );

        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        if ($result->status >= 200 && $result->status <= 210){
            if ($result->data->status == 'processing' || $result->data->status == 'paid'){
                return array(
                    'error' => false,
                    'id' => $result->data->_id
                );
            }else{
                return array(
                    'error' => true,
                    'message' => 'Erro no pagamento: '.$result->data->creditCard->reject->reason
                );
            }

        }else{
            return array(
                'error' => true,
                'message' => 'Erro no pagamento: '.$result->data->error->details[0]->context->message
            );
        }
    }

    /**
     * Realizar o cancelamento de uma fatura
     * @param $invoice_id
     * @return mixed
     */

    public function cancelInvoice($invoice_id){
        $path = '/v2/invoices/cancel/'.$invoice_id;

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );

        $result =  $this->_api_service->put($this->get_base_url('api'), $path, [], $header);

        if ($result->status >= 200 && $result->status <= 210){
            return array(
                'error' => false,
                'id' => $result->data->_id
            );
        }else{
            return array(
                'error' => true,
                'message' => $result->data->message
            );
        }

    }

}

class Lytex_pagamentos_Card_API extends Card_lytex_Gateway {


    /**
     * Informa a URL correta da API
     * @param $metod
     * @return string
     */
    public function get_base_url($metod) {
        if($this->sandbox == "no"){
            return 'https://'.$metod.'-pay.lytex.com.br';
        }else{
            return 'https://sandbox-'.$metod.'-pay.lytex.com.br';
        }
    }

    /**
     * Criar o token de acesso a API Lytex Pagamentos
     * @return mixed
     */
    public function post_access_token() {
        $path = '/v1/oauth/obtain_token';
        $body =  array(
            "grantType"     => "clientCredentials",
            "clientId"      => $this->Client_ID,
            "clientSecret"  => $this->Client_Secret,
            "scopes"        =>[
                "client",
                "invoice",
                "paymentLink",
                "product"
            ]
        );
        $result =  $this->_api_service->post($this->get_base_url('auth'), $path, $body);

        return $result->data->accessToken;

    }

    /**
     * Remove da string todos os careceres especiais,
     * basta passar como parâmetro a string que deseja remover os careceres
     * @param $str
     * @return array|string|string[]|null
     */
    function limpar_texto($str){
        return preg_replace("/[^0-9]/", "", $str);
    }

    /**
     * Undocumented function
     *
     * @param [type] $id_invoices
     * @param [type] $date
     * @return void
     */
    public function segunda_via($id_invoices, $date){
        $path = '/v1/invoices/'.$id_invoices;
        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $datetime = $date ."T23:59:00.000Z";
        $body = array(
            "dueDate"=> $datetime
        );
        $result =  $this->_api_service->put($this->get_base_url('api'), $path, $body, $header);
        return $result->data;
    }


    /**
     * Gerar uma fatura para o cliente
     * @param $post
     * @param $itens
     * @return mixed
     */
    public function post_create_invoice($post = null, $itens = null, $referenceId = null){

        $path = '/v2/invoices';

        if($post['billing_first_lytex_cpf']){

            $_cpf = $this->limpar_texto($post['billing_first_lytex_cpf']);
            $_typePersona = $post['billing_first_lytex_select_cpf'];
            $_number_address = $post['billing_lytex_number_address'];
            $_bairro_address = $post['billing_lytex_bairro_address'];

        }elseif($post['billing_persontype']){

            $_number_address = $post['billing_number'];
            $_bairro_address = $post['billing_neighborhood'];

            $persona = $post['billing_persontype'];
            switch ($persona) {
                case '1':
                    $_typePersona = "pf";
                    $_cpf = $this->limpar_texto($post['billing_cpf']);
                    break;

                case '2':
                    $_typePersona = "pj";
                    $_cpf = $this->limpar_texto($post['billing_cnpj']);
                    break;
            }
        }

        $_cellphone = $this->limpar_texto($post['billing_phone']);

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $vencimento = 'P'.$this->duedate.'D';
        $datetime = new DateTime();
        $datetime->add(new DateInterval($vencimento));
        $order_id = "$referenceId";

        $body = array(
            "referenceId" => $order_id,
            "client"=> [
                "treatmentPronoun"=> "you",
                "name"=> $post['billing_first_name'] . " " . $post['billing_last_name'], //Name last Name OK
                "type"=> $_typePersona, //type document OK
                "cpfCnpj"=> $_cpf, // CPF/CNPJ OK
                "email"=> $post['billing_email'], //E-mail OK
                "cellphone"=> $_cellphone, //CellPhone OK
                "address"=> [
                    "number"=> $_number_address,
                    "zip"=> str_replace('-','', $post['billing_postcode']),
                    "city"=> $post['billing_city'],
                    "street"=> $post['billing_address_1'],
                    "state"=> $post['billing_state'],
                    "zone"=> $_bairro_address
                ]
            ],
            "items"=> $itens,
            "dueDate"=> $datetime->format(DateTime::ATOM),
            "cancelOverdueDays" => 1,
            "paymentMethods"=> [
                "pix"=> [
                    "enable"=> $post['payment_method'] == "lytex" ? true: false
                ],
                "boleto"=> [
                    "enable"=> $post['payment_method'] == "lytex_billet" ? true: false
                ],
                "creditCard"=> [
                    "enable"=> $post['payment_method'] == "lytex_card" ? true: false,
                    "maxParcels"=> 1,
                    "isRatesToPayer" => false
                ]
            ],


        );
        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        return $result->data;
    }

    /**
     * validar os dados envido via hook da API
     * @param $data
     * @return mixed
     */
    public function validate_hook_data($data){
        //pegando a ID da fatura
        $invoiceId = $data['data']['invoiceId'];
        $path = '/v1/invoices/'.$invoiceId;
        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "recipientId"   => $this->recipientId,
            "Content-Type"  => "application/json"
        );
        $result =  $this->_api_service->get($this->get_base_url('api'), $path, $header);
        $response = array(
            'id'=> $result->data->referenceId,
            'status'=> $result->data->status
        );
        return $response;
    }

    /**
     * Gerar o token do cartão de crétido para realizar o pagamento da fatura
     * @param $card_data
     * @return mixed
     */

    public function create_card_token($card_data){
        $path = '/v2/subscriptions/card_token';

        $body = array(
            "cpfCnpj" => $this->limpar_texto($card_data['cpfCnpj']),
            "number" => $this->limpar_texto($card_data['number']),
            "holder" =>$card_data['holder'],
            "expiry" => $this->limpar_texto($card_data['expiry']),
            "cvc" => $this->limpar_texto($card_data['cvc']),
            "_clientId" => (string) $card_data['_clientId']
        );

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );
        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        if ($result->status >= 200 && $result->status <= 210){
            return array(
                'error' => false,
                'token' => $result->data->_id
            );
        }else{
            return array(
                'error' => true,
                'message' => 'Erro nos dados do cartão: '.$result->data->error->details[0]->context->message
            );
        }
    }

    /**
     * Realizar o pagamento da fatura utilizando o token do cartão gerado
     * @param $invoice_id
     * @param $token
     * @return mixed
     */

    public function payInvoiceCardToken($invoice_id, $token){
        $path = '/v2/subscriptions/managed/pay';

        $body = array(
            "_invoiceId" => $invoice_id,
            "_cardTokenId" => $token
        );

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );

        $result =  $this->_api_service->post($this->get_base_url('api'), $path, $body, $header);
        if ($result->status >= 200 && $result->status <= 210){
            if ($result->data->status == 'processing' || $result->data->status == 'paid'){
                return array(
                    'error' => false,
                    'id' => $result->data->_id
                );
            }else{
                return array(
                    'error' => true,
                    'message' => 'Erro no pagamento: '.$result->data->creditCard->reject->reason
                );
            }

        }else{
            return array(
                'error' => true,
                'message' => 'Erro no pagamento: '.$result->data->error->details[0]->context->message
            );
        }
    }

    /**
     * Realizar o cancelamento de uma fatura
     * @param $invoice_id
     * @return mixed
     */

    public function cancelInvoice($invoice_id){
        $path = '/v2/invoices/cancel/'.$invoice_id;

        $header= array(
            "Authorization" => "Bearer " . $this->post_access_token(),
            "Content-Type"  => "application/json"
        );

        $result =  $this->_api_service->put($this->get_base_url('api'), $path, [], $header);

        if ($result->status >= 200 && $result->status <= 210){
            return array(
                'error' => false,
                'id' => $result->data->_id
            );
        }else{
            return array(
                'error' => true,
                'message' => $result->data->message
            );
        }

    }

}



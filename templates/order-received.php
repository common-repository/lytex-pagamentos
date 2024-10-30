<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$_id_ordem = $order->data['id'] != null ? $order->data['id'] : $id_order;


?>
<style>
    .containner-lytex-pagamento {
        background: transparent;
        border: 1px solid #00000017;
        min-height: 200px;
        padding: 30px;
        margin: 30px;
        border-radius: 10px;
        box-shadow: 0 20px 50px rgb(0 0 0 / 7%);
    }
    img.logo-pagina-de-obrigado-lytex {
        max-width: 150px;
    }
    .copy-ticket-code-lytex {
        margin-top: 30px;
        text-align: center;
    }
    .content-ticket-code-lytex {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        box-shadow: 0 20px 50px rgb(0 0 0 / 6%);
    }
    button.button-copy-ticket-lytex {
         background: #004CFF;
         border: none;
         width: 200px;
         height: 50px;
         border-radius: 0px 20px 20px 0px;
         color: white;
         font-size: 1rem;
         font-weight: 800;
     }
    .logo-lytex-pagamentos {
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .lytex-container-information-pay{
        background: #004CFF;
        color: white;
        padding: 10px 45px 10px 45px;
        display: flex;
        border-radius: 35px;
        margin-top: 20px;
        align-items: center;
        flex-direction: row;
        align-content: center;
    }
    .lytex-information-pay {
        text-align: left;
        margin-left: 25px;
    }
    a.lytex-link-boleto {
        color: white;
    }
    a.lytex-link-boleto:hover {
        color: #ff6900;
        text-decoration: none;
    }
    .img-code-bar-code-lytex {
        display: flex;
        justify-content: center;
    }
    @media (max-width: 600px){
        .containner-lytex-pagamento {
            padding: 15px;
            margin: 0px;
        }
        .logo-lytex-pagamentos {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content-ticket-code-lytex {
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 20px 50px rgb(0 0 0 / 6%);
            flex-direction: column;
        }
        button.button-copy-ticket-lytex {
            background: #004CFF;
            border: none;
            width: 100%;
            height: 50px;
            border-radius:5px;
            color: white;
            font-size: 1rem;
            font-weight: 800;
        }

        .lytex-container-information-pay {
            background: #004CFF;
            color: white;
            padding: 30px 5px 30px 5px;
            display: flex;
            border-radius: 35px;
            margin-top: 20px;
            flex-direction: column;
            align-content: center;
            align-items: center;
        }
        .lytex-information-pay {
            text-align: center;
            margin-left: 0px;
        }
    }

</style>



<div>
    <div class="containner-lytex-pagamento">
        <div class="row-lytex-pagamentos">
            <div class="logo-lytex-pagamentos">
                <img class="logo-pagina-de-obrigado-lytex" src="<?php echo plugins_url( 'assets/img/', plugin_dir_path( __FILE__ ));?>logo-dark.png"  alt="Lytex Soluções de Pagamentos">
            </div>
            <?php if($PaymentMethod == "billet"){ 
                    $digitableLine = get_post_meta($_id_ordem, 'digitableLine', true);
                    $barcode = "https://public-api-pay.lytex.com.br/v1/barcode/". get_post_meta($_id_ordem, 'barcode', true);
                            
                ?>
                <!--Pagamentos via boleto Bancario-->
                <div class="copy-ticket-code-lytex">
                    <div class="img-code-bar-code-lytex">
                        <img class="bar-code-lytex" src="<?php echo esc_url( $barcode ); ?>" alt="codigo de barras para o pagamento do boleto">
                    </div>
                    <div class="content-ticket-code-lytex"> 

                        <input id="copy_ticket_lytex" class="value-ticket-code" type="text" value="<?php echo esc_attr( $digitableLine ); ?>" id="myInput">
                        <button id="copy-code" class="button-copy-ticket-lytex">Copiar Codigo</button>
                    </div>

                    <div class="lytex-container-information-pay">
                        <div class="lytex-icon-pay">
                        <img class="icone-pagina-de-obrigado-lytex" src="<?php echo plugins_url( 'assets/img/', plugin_dir_path( __FILE__ ));?>icon-boleto.png"  alt="Lytex Soluções de Pagamentos">
                        </div>
                        <div class="lytex-information-pay">
                            <b>Atenção:</b> Boleto bancário</br>
                            <span>Clique no link abaixo e pague o boleto pelo seu aplicativo de internet banking. Se preferir, você pode imprimir e pagar o boleto em qualquer agência bancária ou lotérica.
                             </br>    <a class="lytex-link-boleto" href="#"><b>Baixar seu boleto aqui...</b></a>

                            </span>
                        </div>
                    </div>
                </div>




            <?php } elseif ($PaymentMethod == "pix"){
                $CodePix = get_post_meta($_id_ordem, 'CodePix', true);
                $qrcode = base64_encode($CodePix);
                $urlqrcode = "https://public-api-pay.lytex.com.br/v1/qrcode/". $qrcode;
                
                 ?>
                <!--Pagamentos via PIX-->



                <div class="copy-ticket-code-lytex">
                    <div class="img-code-bar-code-lytex">
                         <img class="qr-code-lytex" src="<?php echo esc_url( $urlqrcode ); ?>" alt="Imagem qrcode para o pagamento via PIX">
                    </div>
                    <div class="content-ticket-code-lytex">
                        <input id="copy_ticket_lytex" class="value-ticket-code" type="text" value="<?php echo esc_attr( $CodePix ); ?>" id="myInput">
                        <button id="copy-code" class="button-copy-ticket-lytex">Copiar Codigo</button>
                    </div>
                    <div class="lytex-container-information-pay">
                        <div class="lytex-icon-pay">
                        <img class="icone-pagina-de-obrigado-lytex" src="<?php echo plugins_url( 'assets/img/', plugin_dir_path( __FILE__ ));?>icon-pix.png"  alt="Lytex Soluções de Pagamentos">
                        </div>
                        <div class="lytex-information-pay">
                            <b>Atenção:</b> Pix</br>
                            <span>Escanei o Qr-code e pague o boleto pelo seu aplicativo de internet banking. Se preferir, você pode copie o código e cole no aplicativo bancário do seu celular.
                             </br>    <b>Tempo de expiração:</b> 5 minutos

                            </span>
                        </div>
                    </div>
                </div>




            <?php }elseif ($PaymentMethod == "credit_card"){?>
                <div class="copy-ticket-code-lytex">
                    <div class="lytex-container-information-pay">
                        <div class="lytex-information-pay">
                            <p>Você efetuou o pagamento por meio de cartão de crédito utilizando a plataforma Lytex Pagamentos. Assim que a operadora confirmar a transação, procederemos com o processamento do seu pedido.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

</div>

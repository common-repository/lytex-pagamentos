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

                <div class="copy-ticket-code-lytex">
                    <div class="lytex-container-information-pay">
                        <div class="lytex-information-pay">
                            <p>Você efetuou o pagamento por meio de cartão de crédito utilizando a plataforma Lytex Pagamentos. Assim que a operadora confirmar a transação, procederemos com o processamento do seu pedido.</p>
                        </div>
                    </div>
                </div>
        </div>

    </div>

</div>

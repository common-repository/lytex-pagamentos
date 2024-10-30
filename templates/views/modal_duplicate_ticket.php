<style>
    img.logo-pagina-de-obrigado-lytex {
        max-width: 150px;
        display: none;
    }
    img.bar-code-lytex {
        max-width: 100%;
    }
    .containner-lytex-pagamento {
        background: transparent !important;
        border: none #00000000 !important;
        min-height: 0px !important;
        padding: 0px !important;
        margin: 0px !important;
        border-radius: 0px !important;
        box-shadow: 0 20px 50px rgb(0 0 0 / 0%) !important;
    }
    input#copy_ticket_lytex {
        height: 49px !important;
    }
    .modal-window {
        position: fixed;
        background-color: rgb(0 0 0 / 20%);
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 9999999999;
        visibility: hidden;
        opacity: 0;
        pointer-events: auto;
        transition: all 0.3s;
        text-align: center;
    }
    .container-logo-lytex img {
        max-width: 30%;
    }
    .modal-window:target {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
    }
    .modal-window > div {
        width: 400px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 2em;
        background: white;
    }
    .modal-window header {
        font-weight: bold;
    }
    .modal-window h1 {
        font-size: 150%;
        margin: 0 0 15px;
    }

    .modal-close {
        color: #aaa;
        line-height: 50px;
        font-size: 80%;
        position: absolute;
        right: 0;
        text-align: center;
        top: 0;
        width: 70px;
        text-decoration: none;
    }
    .modal-close:hover {
        color: black;
    }


    .modal-window > div {
        border-radius: 1rem;
    }

    .modal-window div:not(:last-of-type) {
        margin-bottom: 15px;
    }

    .logo {
        max-width: 150px;
        display: block;
    }

    small {
        color: #797979;
    }

    .btn {
        background-color: white;
        padding: 1em 1.5em;
        border-radius: 0.5rem;
        text-decoration: none;
    }
    .btn i {
        padding-right: 0.3em;
    }


    .group-input-form-lytex {
        display: flex;
        flex-direction: column;
        width: 100%;
        text-align: left;
        font-size: 1rem;
        font-weight: 300;
    }

    .group-input-form-lytex input {
        width: 100%;
        height: 39px;
        border-radius: 8px;
    }

    .container-submit-lytex input {
        background: linear-gradient(60deg, rgb(112 121 245) 0%, rgb(93 88 204) 100%);
        color: white;
        font-weight: 700;
        width: 200px;
        height: 40px;
        border-radius: 11px;
        border: none;
        cursor: pointer;
    }

</style>
<div id="open-modal" class="modal-window">
    <div>
        <a href="#" id="remove_modal" title="Close" class="modal-close">Fechar</a>
        <div class="container-logo-lytex">
            <img class="lytex-pagamentos-logo-" src="<?php echo plugins_url( 'assets/img/', plugin_dir_path( __DIR__ ));?>logo-dark.png" alt="Logo, Lytex Pagamentos">
        </div>
        <div class="build-html-lytex">
            <div class="build-lytex-form">
                <p>Gerar segunda via de boleto da <b>Ordem #<?php  echo esc_html($order->data['id']);?></b></p>
                <div class="group-input-form-lytex">
                    <label">Dia vencimento</label>
                    <input name="id_order" class="id_order_lytex" value="<?php echo esc_html($order->data['id']); ?>"  type="hidden">
                    <input name="id_invoices" class="id_invoices_lytex" value="<?php echo esc_html($invoices_id); ?>"  type="hidden">
                    <input name="date_2_via" class="email-lytex" type="date" min="<?php echo esc_html(date("Y-m-d")); ?>" required>
                </div>
                <div class="container-submit-lytex">
                    <input name="action" id="gerar2-via-lytex" type="button" value="Gerar Segunda Via">
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    
    jQuery(document).ready(function(){

        jQuery('#remove_modal').click(function(){
            jQuery('.modal-window').css({
                visibility: 'hidden',
                opacity: '0',
            });
        })
        jQuery('#duplicate_ticket_lytex').click(function(){
            jQuery('.modal-window').css({
                visibility: 'visible',
                opacity: '1',
            });
        })




            jQuery('#gerar2-via-lytex').click(function(){
            console.log("click 2 via de boleto");
            var   date2via      = jQuery('.email-lytex').val();
            var   invoices_id   = jQuery('.id_invoices_lytex').val();
            var   id_order      = jQuery('.id_order_lytex').val();
            var img_foguete ="<img class='lytex-rocket-image' src='https://www.imagensanimadas.com/data/media/600/foguete-e-onibus-espacial-imagem-animada-0006.gif'>";

             jQuery('.build-lytex-form').remove();
             jQuery('.build-html-lytex').append(img_foguete);

            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                dataType: "json",
                data: {
                    action: "lytex_ajax_to_issue_duplicate_ticket",
                    date2via: date2via,
                    invoices_id: invoices_id,
                    id_order: id_order,

                },
                success: function (data) {
                    jQuery('.lytex-rocket-image').remove();
                    console.log(data);
                    jQuery('.build-html-lytex').append(data);
                }
            });

        })
    });

</script>

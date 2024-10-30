/**
 * Get the form data such as name and email
 *  and try to connect to Lytex Payments
 * consuming the API
 */
function onClick(e) {
    let email_lytex     = document.querySelector(".email-lytex").value;
    let Client_password_lytex  = document.querySelector(".password-lytex").value;

        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('6LdwU5scAAAAAGHCYryak7ZGmonJxhpEUA_ezLIG', {action: 'directLogin'}).then(function(token) {
            // Add your logic to submit to your backend server here.
            //console.log(token)
            var myHeaders = new Headers();
                myHeaders.append("Client-ID", "62c82f656e30f20019c24f0a");
                myHeaders.append("Client-Secret", "kLjEULKYktzUyLCE8b0X4BDRIce5erAzb5iJfNQQKXq1ONlPAQwL6rTCIehz1HNahq3dhW5XDrw3NPy47jnkJxr3IzfIa3WGtYMK9BkOkWigUDo7qAB2ruhp3CAujOV6rIp0hUJ5r9DLXlyBk6fdeErlndceKW1m2vS6H9i5raj47n9th3Ys8L5DuHYauJ6ee3k8BDgXUQMmrULoNdvuty7cyeztik5QgLNC1KyHqv5oEt2BV86yaGgFdOSIGySs");
                myHeaders.append("Content-Type", "application/json");

                var raw = JSON.stringify({
                "email": email_lytex,
                "password": Client_password_lytex,
                "recaptcha": token
                });

                var requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: raw,
                redirect: 'follow'
                };

                fetch("https://auth-pay.lytex.com.br/v1/auth/direct_login", requestOptions)
                .then(response => response.json())
                .then((result) => lytex_Ajax_login_lytex_pagamentos(result))
                .catch(error => console.log('error', error));
            });
        });
}




function remove_modal(){
    document.querySelector('#open-modal').remove('div');
}
function lytex_Ajax_login_lytex_pagamentos(result){
    jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            dataType: "json",
            data: {
                action: "teste_ajax",
                response: result,
            },

            success: function (data) {
                console.log(data);
            }
    });
}

jQuery(document).ready(function(){
    let Client_Secret = jQuery("#woocommerce_lytex_Client_Secret").val();
    let Client_ID = jQuery("#woocommerce_lytex_Client_ID").val();
    if(Client_ID == "" && Client_Secret == "" ){
        console.log('mostrar modal');
        jQuery('.modal-window').css({
            visibility: 'visible',
            opacity: '1',
        });
    }else{
        jQuery('.modal-window').css({
            visibility: 'hidden',
            opacity: '0',
        });
    }


  //  teste_ajax();
});

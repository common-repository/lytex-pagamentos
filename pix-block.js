const settings_pix = window.wc.wcSettings.getSetting( 'lytex_data', {} );
const label_pix = window.wp.htmlEntities.decodeEntities( settings_pix.title ) || window.wp.i18n.__( 'My Custom Gateway', 'lytex' );
// const Content = () => {
//     return window.wp.htmlEntities.decodeEntities( settings.description || '' );
// };

// const Content_Pix = () => {
//     return (
//         React.createElement(
//             "div",
//             null,
//             React.createElement(
//                 "p",
//                 { className: "lypay-pix__block-text" },
//                 "Pague atrav\xE9s do PIX"
//             ),
//             React.createElement("input", { type: "hidden", name: "lypay_finalPaymentMethod", id: "radio_lypay_pix", defaultValue: "pix" })
//         )
//     )
// };
const Content_Pix = (props ) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentProcessing } = eventRegistration;
    window.wp.element.useEffect( () => {
        const unsubscribe_pix = onPaymentProcessing( async () => {
            // Here we can do any processing we need, and then emit a response.
            // For example, we might validate a custom field, or perform an AJAX request, and then emit a response indicating it is valid or not.
            const lypay_finalPaymentMethod = 'pix';
            const customDataIsValid = !! lypay_finalPaymentMethod.length;

            if ( customDataIsValid ) {
                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            lypay_finalPaymentMethod,
                        },
                    },
                };
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                message: 'There was an error',
            };
        } );
        // Unsubscribes when this component is unmounted.
        return () => {
            unsubscribe_pix();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentProcessing,
    ] );
    return window.wp.htmlEntities.decodeEntities( settings_pix.description || '' );
};

const Block_Gateway_pix = {
    name: 'lytex',
    label: label_pix,
    content: Object( window.wp.element.createElement )( Content_Pix, null ),
    edit: Object( window.wp.element.createElement )( Content_Pix, null ),
    canMakePayment: () => true,
    ariaLabel: label_pix,
    supports: {
        features: settings_pix.supports,
    },
};
window.wc.wcBlocksRegistry.registerPaymentMethod( Block_Gateway_pix );


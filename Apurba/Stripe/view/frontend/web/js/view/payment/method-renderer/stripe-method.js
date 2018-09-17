/**
 * Inchoo_Stripe Magento JS component
 *
 * @category    Inchoo
 * @package     Inchoo_Stripe
 * @author      Ivan Weiler & Stjepan Udovičić
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $, ko, quote, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Apurba_Stripe/payment/stripe-form',
                source: null,
                saveCard : false,
                stripe: null,
                cardElement: null,
                fieldErrorMessages: {
                    card: ko.observable(false),
                    expiry: ko.observable(false),
                    cvc: ko.observable(false)
                },
                cardRec : {
                    number : ko.observable(null),
                    cvc : ko.observable(null),
                    exp_month : ko.observable(null),
                    exp_year : ko.observable(null)
                },
                token : ko.observable(null)
            },

            /**
             * @returns {exports.initialize}
             */
            initialize: function () {
                this._super();
                //this.initStripe();
                return this;
            },

            initStripe: function(){
                var config = window.checkoutConfig.payment[this.getCode()];
                if (!config) {
                    return;
                }
                
                var self = this;
                require([config.sdkUrl], function () {
                    // Initialise Stripe
                    self.stripe = Stripe(config.publishableKey);
                    
                    // Initialise elements
                    var elements = self.stripe.elements();
                    self.cardElement = elements.create('cardNumber');
                    self.cardElement.mount('#' + self.getCode() + '_cc_number');
                    self.cardElement.on('change', self.onFieldChange('card'));
                    
                    var cardExpiry = elements.create('cardExpiry');
                    cardExpiry.mount('#' + self.getCode() + '_expiry');
                    cardExpiry.on('change', self.onFieldChange('expiry'));
                    
                    var cardCvc = elements.create('cardCvc');
                    cardCvc.mount('#' + self.getCode() + '_cc_cvc');
                    cardCvc.on('change', self.onFieldChange('cvc'));
                    
                });
            },

            /**
             * Return field's error message observable
             */
            getErrorMessageObserver: function (field) {
                return this.fieldErrorMessages[field];
            },
            
            /**
             * Return field change event handler
             */
            onFieldChange: function (fieldName) {
                var errorMessage = this.fieldErrorMessages[fieldName];
                return function (event) {
                    errorMessage(
                        event.error ? event.error.message : false
                    );
                };
            },

            getCode: function() {
                return 'apurba_stripe';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
            /**
             * Get data
             *
             * @returns {Object}
             */
            getData: function () {

                if($('#save_card').is(':checked'))
                    this.saveCard = true;
                else
                    this.saveCard = false;


                var data = {
                    'method': this.item.method,
                    'additional_data': {
                        'source': this.source,
                        'token' : this.token,
                        'save_card' : this.saveCard,
                        'card_number': this.cardRec.number,
                        'card_cvc' : this.cardRec.cvc,
                        'exp_year' : this.cardRec.exp_year,
                        'exp_month': this.cardRec.exp_month
                    }
                };
                
                return data;
            },

            /**
             * Set source
             * 
             * @param {String} source
             */
            setSource: function (source) {
                this.source = source;
            },

            setToken: function(token){
            	this.token = token;
            },

            /**
             * Place the order
             * 
             * @param {Object} data
             */
            placeOrderClick: function () {
                if (!this.stripe || !this.cardElement) {
                    console.err('Stripe or CardElement not found');
                    return;
                }
                console.log(this.cardElement);
                var self = this;
               	this.stripe.createToken(this.cardElement).then(function(result) {
				  if(result.error)
				  	console.log(result.error);
				  else
				  	self.setToken(result.token.id);
				});
                
                var cardData = { };
                var billingAddress = quote.billingAddress();
                if (billingAddress) {
                    cardData.owner = {
                        name: billingAddress.firstname + ' ' + billingAddress.lastname,
                        phone: billingAddress.telephone,
                        address: {
                            line1: billingAddress.street[0],
                            line2: billingAddress.street.length > 1 ? billingAddress.street[1] : null,
                            city: billingAddress.city,
                            state: billingAddress.region,
                            postal_code: billingAddress.postcode,
                            country: billingAddress.countryId,
                        }
                    };
                }

               
                this.stripe.createSource(this.cardElement, cardData)
                    .then(function (result) {
                        if (result.error) {
                            var message = result.error.message;
                            if (result.error.type == 'validation_error') {
                                message = $t('Please verify you card information.');
                            }
                            messageList.addErrorMessage({
                                message: message
                            });
                            return;
                        }
                        
                        self.cardRec.number(result.source.card.last4);
                        self.cardRec.exp_month(result.source.card.exp_month);
                        self.cardRec.exp_year(result.source.card.exp_year);
                        self.setSource(result.source.id);
                        self.placeOrder();
                    });
            },

        });
    }
);

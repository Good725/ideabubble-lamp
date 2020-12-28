/*
ts:2019-10-03 10:50:00
*/

UPDATE plugin_formbuilder_forms SET `fields` = REPLACE(`fields`, '<label for="payment_form_method_stripe">Stripe</label>', '<label for="payment_form_method_stripe">Stripe</label><li> <fieldset id="payment_form_method_stripe_inputs" class="hidden"> </fieldset> </li>') WHERE form_name='PaymentFormQuickOrder';

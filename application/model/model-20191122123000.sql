/*
ts:2019-11-22 12:30:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`)
VALUES ('checkout_deposit_text', 'Deposit text', '$1 deposit', '$1 deposit', '$1 deposit', '$1 deposit', '$1 deposit', 'both', 'Text to appear when displaying the deposit on the checkout or cart. Do not remove the <cede>$1</code> or change its currency. It will automatically be replaced with the relevant amount of money.', 'text', 'Checkout', 0);


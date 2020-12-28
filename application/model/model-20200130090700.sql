/*
ts:2020-01-30 09:07:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`,
                                      `default`, `note`, `type`, `group`, `options`)
VALUES ('captcha_position',
        'Captcha frontend position',
        'billing_address',
        'billing_address',
        'billing_address',
        'billing_address',
        'billing_address',
        'Position of the captcha on the frontend checkout',
        'dropdown',
        'Captcha',
        '{"billing_address":"Below billing address", "cart_section":"Cart section"}');

UPDATE `engine_settings`
SET `variable` = 'captcha_frontend_checkout_position',
    `name`     = 'Captcha frontend checkout position'
WHERE (`variable` = 'captcha_position');

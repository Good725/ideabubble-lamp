/*
ts:2020-02-07 10:22:00
*/

UPDATE `engine_settings`
SET `value_live`='cart_section',
    `value_stage`='cart_section',
    `value_test`='cart_section',
    `value_dev`='cart_section',
    `default`='cart_section'
WHERE `variable` = 'captcha_frontend_checkout_position';


/*
ts:2019-05-14 10:00:00
*/

-- Ideally we want a GUI where the user can select which fields are to display on the checkout.
-- We're using this hardcode for now.

INSERT INTO
  `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES (
  'checkout_customization',
  'Checkout field customisation',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Show different fields on the checkout, depending on your selection.',
  'dropdown',
  'Bookings',
  '{\"\":\"Default\",\"bcfe\":\"Ballyfermot College\",,\"bc_language\":\"Brookfield College Language\",\"sls\":\"Shandon Language Solutions\"}'
);

UPDATE
  `engine_settings`
SET
  `options`  = '{\"\":\"Default\",\"bcfe\":\"Ballyfermot College\",\"bc_language\":\"Brookfield College Language\",\"sls\":\"Shandon Language Solutions\"}'
WHERE
  `variable` = 'checkout_customization'
;
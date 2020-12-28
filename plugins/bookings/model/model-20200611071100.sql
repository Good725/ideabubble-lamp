/*
ts:2020-06-11 07:11:00
*/


UPDATE
  `engine_settings`
SET
  `options`  = '{\"\":\"Default\",\"bcfe\":\"Ballyfermot College\",\"bc_language\":\"Brookfield College Language\",\"sls\":\"Shandon Language Solutions\",\"lsm\":\"Limerick School Of Music\",\"itt\":\"Irish Times Training\", \"ibec\": \"IBEC\"}'
WHERE
  `variable` = 'checkout_customization'
;

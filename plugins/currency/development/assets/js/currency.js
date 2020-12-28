function setPreferredCurrency(currency)
{
    $.post(
        '/frontend/currency/set_preferred_currency',
        {
            currency: currency
        },
        function (response) {
            window.location.reload();
        }
    );
}
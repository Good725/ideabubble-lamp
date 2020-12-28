//
// GENERAL
//

/**
 *
 * @param {int} number
 * @param {int} decimalPositions
 * @returns {string}
 */
function truncateNumber(number, decimalPositions) {
    var s = number.toString(), m = s.indexOf('.');

    return m == -1 ? s : (decimalPositions == 0 ? s.substring(0, m) : s.substring(0, m + decimalPositions + 1));
}

//
// REQUEST
//

var Request = Request || {};

Request._STATUS_S_ERROR = -1;
Request._STATUS_S_OK    =  0;

/**
 *
 * @param {string} url
 * @param {function} f
 */
Request.makeGETRequest = function(url, f) {
    $.get(url)
        .done(function(response) {
            Request._processResponse(JSON.parse(response), f);
        })
        .fail(function() {
            Alert.displayAlert(Alert.ALERT_ERROR, Alert.NETWORK_ERROR_DEFAULT_MESSAGE);
        });
};

/**
 *
 * @param {string} url
 * @param {object} data
 * @param {function} f
 */
Request.makePOSTRequest = function(url, data, f) {
    $.post(url, data)
        .done(function(response) {
            Request._processResponse(JSON.parse(response), f);
        })
        .fail(function() {
            Alert.displayAlert(Alert.ALERT_ERROR, Alert.NETWORK_ERROR_DEFAULT_MESSAGE);
        });
};

/**
 *
 * @param {string} response
 * @param {function} f
 * @private
 */
Request._processResponse = function(response, f) {
    if (response['status'] == Request._STATUS_S_OK) {
        typeof response['data'] != 'undefined' ? f(response['data']) : f();
    }

    if (typeof response['message'] != 'undefined') {
        Alert.displayAlert((response['status'] == Request._STATUS_S_OK) ? Alert.ALERT_SUCCESS : Alert.ALERT_ERROR, response['message']);
    }
};

//
// ALERT
//

var Alert = Alert || {};

Alert.NETWORK_ERROR_DEFAULT_MESSAGE     = 'Network error. Check your connection.';
Alert.OPERATION_SUCCESS_DEFAULT_MESSAGE = 'Operation succeeded.';

Alert.ALERT_ERROR   = -1;
Alert.ALERT_SUCCESS =  0;

/**
 *
 * @param {int} alertType
 * @param {string} alertMessage
 */
Alert.displayAlert = function(alertType, alertMessage) {
    var alertClass = '';

    $(".alert").remove();

    switch (alertType) {
        case Alert.ALERT_SUCCESS:
            alertClass = 'alert-success';
            break;

        case Alert.ALERT_ERROR:
            alertClass = 'alert-error';
            break;

        default:
            break;
    }

    if (alertClass != '') {
        $("#main").prepend('<div class="alert ' + alertClass + '"><a class="close" data-dismiss="alert">&times;</a>' + alertMessage + '</div>');
    }
};

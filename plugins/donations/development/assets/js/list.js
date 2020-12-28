$(document).ready(function()
{
	var $table = $('#list-donations-table');
	$("#status-donation-form").on("submit", function(e){
        e.preventDefault();
        var form = this;
        var data = $(this).serialize();
        $.post(
            this.action,
            data,
            function (response) {
				window.location.reload();
            }
        );
        return false;
    });

    $("#mute-donation-button").on("click", function(e) {
        $("#status-donation-form [name=mute]").val("1");
    });
});

$(document).on("click", "a.mobile", function(){
    var $tbody = $("#history-donation-modal tbody");
    $tbody.html("<tr><td colspan='7'>loading...</td>");
    var mobile = $(this).data("number");
    $("#history-donation-modal .modal-title span.number").html(mobile);

    $.post(
        "/admin/donations",
        {
            output: "json",
            mobile: mobile
        },
        function (response) {
            var status_labels = {
                'Processing': 'Received',
                'Rejected': 'Rejected',
                'Confirmed': 'Approved',
                'Completed': 'Approved',
                'Offline': 'Offline'
            };
            $tbody.html("");
            var paid = 0;
            var cost = 0;
            for (var i in response) {
                if (response[i].cost) {
                    cost += parseFloat(response[i].cost);
                }
                if (response[i].paid) {
                    paid += parseFloat(response[i].paid);
                }
                $tbody.append(
                    '<tr>' +
                        '<td>' + response[i].message + '</td>' +
                        '<td>' + response[i].created + '</td>' +
                        '<td>' + (response[i].status ? status_labels[response[i].status] : '' )+ '</td>' +
                        '<td>' + (response[i].note ? response[i].note : '') + '</td>' +
                        '<td>' + (response[i].product ? response[i].product : '') + '</td>' +
                        '<td>' + (response[i].cost ? response[i].cost : '') + '</td>' +
                    '</tr>'
                )
            }

            $("#history-donation-modal tfoot .cost").html(cost.toFixed(2));
            $("#history-donation-modal tfoot .paid").html(paid.toFixed(2));
        }
    )
});

$(document).on("click", "#list-donations-table button", function(){
    $("#status-donation-modal [name=contact_id]").val($(this).data("contact_id"));

    if ($(this).hasClass("list-complete-button-a")) {
        var data = {};
        data.status = $(this).data("status");
        data.id = $(this).data("id");
        data.reply = confirm_template.message;

        this.disabled = true;
        
        $.post(
            "/admin/donations/status_set",
            data,
            function (response) {
                window.location.reload();
            }
        );
    } else {
        $('#status-donation-modal [name=status]').val($(this).data("status"));
        $('#status-donation-modal [name=id]').val($(this).data("id"));

        $("#status-donation-modal .form-group.mobile").addClass("hidden");
        $("#status-donation-modal [name=note]").val("");
        $("#status-donation-modal [name=reply]").val("");
        $("#status-donation-modal input[name=paid]").val("");
        $("#status-donation-modal [name=mobile]").attr("placeholder", "");
        $("#status-donation-modal .form-group.note").removeClass("hidden");
        $("#mute-donation-button").addClass("hidden");

        $("#status-donation-modal .form-group.paid").hide();
        switch ($(this).data("status")) {
            case "Confirmed":
                $("#status-donation-modal .modal-title").html("Confirm Request");
                $("#update-donation-button").html("Confirm");
                $("#status-donation-modal [name=reply]").val(confirm_template.message);
                break;
            case "Rejected":
                $("#mute-donation-button").removeClass("hidden");
                $("#status-donation-modal .modal-title").html("Reject Request");
                $("#update-donation-button").html("Reject");
                $("#status-donation-modal [name=reply]").val(reject_template.message);

                if ($(this).data("reply") == "Yes") {
                    $("#status-donation-modal .modal-title").html("Reply to Request");
                    $("#update-donation-button").html("Reply");
                    $("#status-donation-modal [name=reply]").val(invalid_template.message);
                }
                break;
            case "Completed":
                $("#status-donation-modal input[name=paid]").val($(this).data("cost"));
                $("#status-donation-modal .modal-title").html("Approve Request");
                $("#update-donation-button").html("Approve");
                $("#status-donation-modal [name=reply]").val(complete_template.message);
                $("#status-donation-modal .form-group.paid").show();
                break;
            default:
                if ($(this).data("reply") == "Yes") {
                    $("#status-donation-modal .modal-title").html("Reply to Request");
                    $("#update-donation-button").html("Send");
                    $("#status-donation-modal [name=reply]").val("");
                    $("#status-donation-modal [name=mobile]").attr("placeholder", $(this).data("mobile"));
                    $("#status-donation-modal .form-group.note").addClass("hidden");
                    $("#status-donation-modal .form-group.mobile").removeClass("hidden");
                }
                break;
        }
    }
});


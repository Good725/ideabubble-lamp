$(document).ready(function() {
    Request.makeGETRequest('/admin/files/ajax_get_path_breadcrumbs?directory_id=' + $('#parentId').val(), function(data) {
        renderPathBreadcrumbs(data);
    });

    if ($('#versionsTable').length > 0) {
        Versions.buildTable();
    }

    $('#versionFile').on('change', function() {
        $('#txtVersionFile')
            .val($(this).val())
            .trigger('keyup');
    });


    $("#frmAddEditFile").validate({

    });
});

/**
 *
 * @param {Array} breadcrumbsArray
 */
function renderPathBreadcrumbs(breadcrumbsArray) {
    var html = '';

    html += '<ul class="breadcrumb">';

    for (var i = 0, m = breadcrumbsArray.length; i < m; i++) {
        html += '<li><a href="/admin/files/list_directory?directory_id=' + breadcrumbsArray[i]['id'] + '">' + breadcrumbsArray[i]['name'] + '</a></li>';
    }

    html += '<li class="active"><span>' + ($.trim($('#fileId').val()) == '' ? 'New File' : $('#fileName').val()) + '</span></li>';
    html += '</ul>';

    $('#pathBreadcrumbs').html(html);

    $('.bcLink')
        .off ('click')
        .on  ('click', function() {
            location.href = '/admin/files/list_directory?id=' + $(this).data('id');
        });
}

//
// VERSIONS
//

var Versions = Versions || {};

/**
 *
 */
Versions.buildTable = function() {
    var fileId        = $('#fileId').val();
    var versionsTable = $('#versionsTable');

    Request.makeGETRequest('/admin/files/ajax_get_versions?file_id=' + fileId, function(data) {
        versionsTable.children('tbody').empty();

        for (var i = 0, m = data.length; i < m; i++) {
            var row = '';

            row += '<tr data-id="' + data[i]['id'] + '">';
            row += '<td>' + data[i]['name'] + '</td>';
            row += '<td>' + truncateNumber(data[i]['size'] / 1024, 2) + '</td>';
            row += '<td><input type="radio" class="change-active" name="active"' + (data[i]['active'] == 1 ? 'checked="checked"' : '') + '"/></td>';
            row += '<td><span class="remove-version"><i class="icon-trash"></i></span>&nbsp;<span class="download-version"><i class="icon-download-alt"></i></span>' + '</td>';
            row += '</tr>';

            versionsTable.append(row);
        }

        versionsTable
            .on('click', '.change-active'   , function() {
                var versionId = $(this).parents('tr').data('id');

                Versions._setActiveVersion(fileId, versionId);
            })
            .on('click', '.remove-version'  , function() {
                var versionId = $(this).parents('tr').data('id');

                Versions._removeVersion(versionId);
            })
            .on('click', '.download-version', function() {
                var versionId = $(this).parents('tr').data('id');

                Versions._downloadVersion(versionId);
            });
    });
};

/**
 *
 * @param {int} fileId
 * @param {int} versionId
 * @private
 */
Versions._setActiveVersion = function(fileId, versionId) {
    Request.makePOSTRequest('/admin/files/ajax_set_active_version', { file_id : fileId , version_id : versionId }, function() {});
};

/**
 *
 * @param {int} versionId
 * @private
 */
Versions._removeVersion = function(versionId) {
    Request.makePOSTRequest('/admin/files/ajax_remove_version', { version_id : versionId }, function() {
        Versions.buildTable();
    });
};

/**
 *
 * @param {int} versionId
 * @private
 */
Versions._downloadVersion = function(versionId) {
    location.href = '/admin/files/download_version?version_id=' + versionId;
};

$(document).ready(function() {
    Files.initializeTable();

    $('#btnCreateDirectory').on('click', function() {
        var txtDirectoryName = $('#txtDirectoryName');
        var directoryName    = $.trim(txtDirectoryName.val());

        if (directoryName != '') {
            Files.createDirectory(directoryName);

            txtDirectoryName.val('');
        }
    });

    $('#btnAddFile').on('click', function() {
        var parentId = Files.getCurrentDirectoryId();

        if (parentId != -1) {
            location.href = '/admin/files/add?parent_id=' + parentId;
        }
    });
});

//
// FILES
//

var Files = Files || {};

Files._FILE_TYPE_DIRECTORY = 0;
Files._FILE_TYPE_REGULAR   = 1;

Files._currentDirectoryId  = -1;

/**
 *
 */
Files.initializeTable = function() {
    $('#filesTable').ready(function () {
        Files._currentDirectoryId = $('#directoryId').val();

        Files._initializeTable    ();
        Files._showPathBreadcrumbs();
    });
};

/**
 *
 * @param {string} directoryName
 */
Files.createDirectory = function(directoryName) {
    Request.makePOSTRequest('/admin/files/ajax_create_directory', { parent_id : Files._currentDirectoryId, name : directoryName }, function() {
        Files._listCurrentDirectory();
    });
};

/**
 *
 * @returns {int}
 */
Files.getCurrentDirectoryId = function() {
    return Files._currentDirectoryId;
};

/**
 *
 * @private
 */
Files._listCurrentDirectory = function() {
    Files._refreshTable       ();
    Files._showPathBreadcrumbs();
};

/**
 *
 * @private
 */
Files._initializeTable = function() {
    var $table = $('#filesTable').dataTable();
    var oSettings  = $table.fnSettings();
    $table.fnDestroy();

    oSettings.bServerSide = true;
    oSettings.sAjaxSource = '/admin/files/ajax_list_directory?directory_id=' + Files._currentDirectoryId;

    oSettings.aoColumns[0].bVisible    = false;
    oSettings.aoColumns[0].bSearchable = false;
    oSettings.aoColumns[0].bSortable   = false;

    oSettings.aoColumns[1].bSearchable = false;
    oSettings.aoColumns[1].bSortable   = false;

    oSettings.aoColumns[2].sWidth      = '30%';

    oSettings.oLanguage.sSearch        = 'Search by name:';

    oSettings.fnRowCallback = function(nRow, aData) {
        var removeFile, downloadFile;

        removeFile   = '<span class="remove-file"><i class="icon-trash"></i></span>&nbsp;';
        if (aData[8]== "1"){
            downloadFile = '<span title="" class="download-file"><i class="icon-download-alt"></i></span>';
        }
        else
        {
            downloadFile = '<span title="">n/a</span>';
        }


        $('td:eq(0)', nRow).html(aData[1] == Files._FILE_TYPE_DIRECTORY ? '<i class="icon-folder-close"></i>' : '');
        $('td:eq(2)', nRow).html(aData[3] === null ? '' : truncateNumber(aData[3] / 1024, 2));
        $('td:eq(6)', nRow).html(removeFile + (aData[1] == Files._FILE_TYPE_REGULAR ? downloadFile : ''));
        $('td:eq(7)', nRow).html(aData[7]);
        $(nRow)
            .attr('data-id'  , aData[0])
            .attr('data-type', aData[1]);
    };

    $table.ib_serverSideTable('/admin/files/ajax_list_directory?directory_id=' + Files._currentDirectoryId, oSettings);
    $table
        .on ('click', 'td:not(:nth-child(7))', function() {
            var parentNode = $(this).parents('tr');

            Files._processClick(parentNode.data('type'), parentNode.data('id'));
        })
        .on ('click', 'span.remove-file'     , function() {
            var parentNode = $(this).parents('tr');

            Files._processRemoveFile(parentNode.data('type'), parentNode.data('id'));
        })
        .on ('click', 'span.download-file'   , function() {
            var parentNode = $(this).parents('tr');

            Files._processDownloadFile(parentNode.data('id'));
        });
};

/**
 *
 * @private
 */
Files._refreshTable = function() {
    var filesTable = $('#filesTable').dataTable();

    filesTable.fnSettings().sAjaxSource = '/admin/files/ajax_list_directory?directory_id=' + Files._currentDirectoryId;
    filesTable.fnFilter('');
};

/**
 *
 * @param {int} type
 * @param {int} id
 * @private
 */
Files._processClick = function(type, id) {
    switch (type)
    {
        case Files._FILE_TYPE_DIRECTORY:
            Files._currentDirectoryId = id;

            Files._listCurrentDirectory();
            break;

        case Files._FILE_TYPE_REGULAR:
            location.href = '/admin/files/edit?parent_id=' + Files._currentDirectoryId + '&file_id=' + id;
            break;

        default:
            break;
    }
};

/**
 *
 * @param {int} id
 * @private
 */
Files._processDownloadFile = function(id) {
    location.href = '/admin/files/download_file?file_id=' + id;
};

/**
 *
 * @param {int} type
 * @param {int} id
 * @private
 */
Files._processRemoveFile = function(type, id) {
    bootbox.confirm('WARNING! This action is not reversible! Are you sure?', function(proceed) {
        if (proceed) {
            switch (type)
            {
                case Files._FILE_TYPE_DIRECTORY:
                    Request.makePOSTRequest('/admin/files/ajax_remove_directory', { directory_id : id }, function() {
                        Files._listCurrentDirectory();
                    });
                    break;

                case Files._FILE_TYPE_REGULAR:
                    Request.makePOSTRequest('/admin/files/ajax_remove_file', { file_id : id }, function() {
                        Files._listCurrentDirectory();
                    });
                    break;

                default:
                    break;
            }
        }
    });
};

/**
 *
 * @private
 */
Files._showPathBreadcrumbs = function() {
    Request.makeGETRequest('/admin/files/ajax_get_path_breadcrumbs?directory_id=' + Files._currentDirectoryId, function(data) {
        Files._renderPathBreadcrumbs(data);
    });
};

/**
 *
 * @param {Array} breadcrumbsArray
 * @private
 */
Files._renderPathBreadcrumbs = function(breadcrumbsArray) {
    var html = '';

    html += '<ul class="breadcrumb">';

    for (var i = 0, m = breadcrumbsArray.length - 1; i < m; i++) {
        html += '<li><a href="#" class="bcLink" data-id="' + breadcrumbsArray[i]['id'] + '">' + breadcrumbsArray[i]['name'] + '</a></li>';
    }

    html += '<li class="active"><span>' + breadcrumbsArray[i]['name'] + '</span></li>';
    html += '</ul>';

    $('#pathBreadcrumbs').html(html);

    $('.bcLink')
        .off ('click')
        .on  ('click', function() {
            Files._currentDirectoryId = $(this).data('id');

            Files._listCurrentDirectory();
        });
};

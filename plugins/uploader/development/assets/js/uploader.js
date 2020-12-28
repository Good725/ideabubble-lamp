//
// UPLOADER
//

/*

    EXAMPLE OF USE
    --------------

    var c = {};

    c.uploaderAction    The PHP script that will process the POST. REQUIRED.
    c.onFileAdded       Every time a file is added, this event will be triggered. OPTIONAL.
    c.onBatchAdded      Every time a set of files (one or more) is added, this event will be triggered. OPTIONAL.
    c.onFileUploaded    Every time a file is uploaded, this event will be triggered. OPTIONAL.
    c.onBatchUploaded   Every time a set of files (one or more) is uploaded, this event will be triggered. OPTIONAL.
    c.multipleUploads   Only for CnS, not for DnD. OPTIONAL. DEFAULT: TRUE.

    UPLOADER.initialize(c);

    (UPLOADER.isDnDSupported()) ? UPLOADER.registerDnD('uploader_dnd') : $('#uploader_dnd').hide();
    UPLOADER.registerCnS('uploader_cns');

    HTML CODE
    ---------

    <!-- DnD -->
    <span class="dnd-file-uploader" id="uploader_dnd">Drop Files Here!</span><br/>

    <!-- CnS -->
    <span class="btn btn-success btn-file-uploader" id="uploader_cns"><span>Add File...</span><br/>

    CSS
    ---

    .dnd-file-uploader {
        border: 2px dotted #d3d3d3;
        border-radius: 4px;
        width: 300px;
        height: 100px;
        line-height: 100px;
        display: block;
        vertical-align: middle;
        text-align: center;
        color: #d3d3d3;
        font-size: 18pt;
    }

 */

var UPLOADER = UPLOADER || {};

// CONSTANTS
UPLOADER.STATUS_NOT_UPLOADED               =  1;
UPLOADER.STATUS_S_OK                       =  0;
UPLOADER.STATUS_E_ERROR                    = -1;
UPLOADER.STATUS_E_FILE_EXISTS              = -2;
UPLOADER.STATUS_E_FILE_TOO_BIG             = -3;
UPLOADER.STATUS_E_FILE_TYPE_NOT_ALLOWED    = -4;
UPLOADER.STATUS_E_DIRECTORY_LIMIT_EXCEEDED = -5;
UPLOADER.STATUS_E_DIRECTORY_SIZE_EXCEEDED  = -6;

// GLOBAL VARIABLES
UPLOADER._uploaderSettings = {};
UPLOADER._filesToUpload    = [];
UPLOADER._nFilesInBatch    =  0;

/**
 *
 * @param {object} c
 */
UPLOADER.initialize = function(c) {
    UPLOADER._uploaderSettings = c;
};

/**
 *
 * @returns {boolean}
 */
UPLOADER.isDnDSupported = function() {
    return ('draggable' in document.createElement('span') && typeof FormData !== 'undefined');
};

/**
 *
 * @param {string} node_id
 */
UPLOADER.registerDnD = function(node_id) {
    if (UPLOADER.isDnDSupported()) {
        document.querySelector('#' + node_id).addEventListener('dragenter', UPLOADER._defaultDnDHandler, false);
        document.querySelector('#' + node_id).addEventListener('dragexit' , UPLOADER._defaultDnDHandler, false);
        document.querySelector('#' + node_id).addEventListener('dragover' , UPLOADER._defaultDnDHandler, false);
        document.querySelector('#' + node_id).addEventListener('drop'     , UPLOADER._handleDrop       , false);
    }
};

/**
 *
 * @param {string} node_id
 */
UPLOADER.registerCnS = function(node_id) {
    var node = $('#' + node_id);

    node
        .css('overflow', 'hidden'  )
        .css('cursor'  , 'pointer' )
        .css('display' , 'block'   )
        .css('position', 'relative');

    UPLOADER._addInput(node);
};

/**
 *
 * @param {int} file_id
 * @returns {string}
 */
UPLOADER.getFileName = function(file_id) {
    var o, s;

    if (file_id >= 0 && file_id < UPLOADER._filesToUpload.length) {
        o = UPLOADER._filesToUpload[file_id].object;
        s = $(o).is('input') ? o.value : o.name;
    }

    return s.lastIndexOf("/") == -1 ? s : s.substring(0, s.lastIndexOf("/"));
};

/**
 *
 * @param {int} file_id
 * @returns {string}
 */
UPLOADER.getServerResponse = function(file_id) {
    var o, r = null;

    if (file_id >= 0 && file_id < UPLOADER._filesToUpload.length) {
        o = UPLOADER._filesToUpload[file_id];
        r = o.serverResponse;
    }

    return r;
};

/**
 *
 * @param {int} file_id
 */
UPLOADER.uploadFile = function(file_id) {
    var o;

    if (file_id >= 0 && file_id < UPLOADER._filesToUpload.length && UPLOADER._filesToUpload[file_id].canBeUploaded) {
        o = UPLOADER._filesToUpload[file_id];

        o.canBeUploaded = false;

        $(o.object).is('input') ? UPLOADER._sendFileUsingIFRAME(o) : UPLOADER._sendFileUsingAJAX(o);
    }
};

/**
 *
 */
UPLOADER.uploadAll = function() {
    var i;

    for (i = 0; i < UPLOADER._filesToUpload.length; i++) {
        if (UPLOADER._filesToUpload[i].canBeUploaded) {
            UPLOADER._nFilesInBatch++;

            UPLOADER.uploadFile(i);
        }
    }
};

/**
 *
 * @param {object} o
 * @returns {int}
 * @private
 */
UPLOADER._addFileToUpload = function(o) {
    var item = {};

    item.id             = UPLOADER._filesToUpload.length;
    item.object         = o;
    item.status         = UPLOADER.STATUS_NOT_UPLOADED;
    item.canBeUploaded  = true;
    item.serverResponse = null;

    UPLOADER._filesToUpload[item.id] = item;

    return item.id;
};

/**
 *
 * @param {object} e
 * @private
 */
UPLOADER._defaultDnDHandler = function(e) {
    e.stopPropagation();
    e.preventDefault ();
};

/**
 *
 * @param {object} e
 * @private
 */
UPLOADER._handleDrop = function(e) {
    var i, j, files = e.dataTransfer.files;

    UPLOADER._defaultDnDHandler(e);

    for (i = 0; i < files.length; i++) {
        j = UPLOADER._addFileToUpload(files[i]);

        if (UPLOADER._uploaderSettings.onFileAdded) {
            UPLOADER._uploaderSettings.onFileAdded(j);
        }
    }

    if (UPLOADER._uploaderSettings.onBatchAdded) {
        UPLOADER._uploaderSettings.onBatchAdded();
    }
};

/**
 *
 * @private
 */
UPLOADER._handleCnS = function() {
    var i, j, files = this.files, parent = $(this).parent('');

    $(this)
        .off('change')
        .remove();

    if (files) {
        for (i = 0; i < files.length; i++) {
            j = UPLOADER._addFileToUpload(files[i]);

            if (UPLOADER._uploaderSettings.onFileAdded) {
                UPLOADER._uploaderSettings.onFileAdded(j);
            }
        }
    } else {
        j = UPLOADER._addFileToUpload(this);

        if (UPLOADER._uploaderSettings.onFileAdded) {
            UPLOADER._uploaderSettings.onFileAdded(j);
        }
    }

    if (UPLOADER._uploaderSettings.onBatchAdded) {
        UPLOADER._uploaderSettings.onBatchAdded();
    }

    UPLOADER._addInput(parent);
};

/**
 *
 * @param {object} node
 * @private
 */
UPLOADER._addInput = function(node) {
    var input;

    if (node.children('input').length == 0) {
        input = $(typeof FormData !== 'undefined' && (typeof UPLOADER._uploaderSettings.multipleUploads == 'undefined' || UPLOADER._uploaderSettings.multipleUploads) ? '<input type="file" multiple="multiple" name="file"/>' : '<input type="file" name="file"/>');

        input
            // CSS (From: http://blueimp.github.io/jQuery-File-Upload/)
            .css('position' , 'absolute'                       )
            .css('right'    , '0'                              )
            .css('top'      , '0'                              )
            .css('margin'   , '0'                              )
            .css('cursor'   , 'pointer'                        )
            .css('opacity'  , '0'                              )
            .css('filter'   , 'alpha(opacity=0)'               )
            .css('transform', 'translate(-300px, 0px) scale(4)')

            // Events
            .on ('change'   , UPLOADER._handleCnS              );

        node.append(input);
    }
};

/**
 *
 * @param {object} o
 * @private
 */
UPLOADER._sendFileUsingIFRAME = function(o) {
    var form   = $('<form action="' + UPLOADER._uploaderSettings.uploaderAction + '" enctype="multipart/form-data" method="post"><input type="hidden" name="XIFRAME"></form>');
    var iframe = $('<iframe style="display: none"></iframe>');

    form.append(o.object);

    iframe.on('load', function() {
        $($(this).contents().get(0).getElementsByTagName('body')[0]).append(form);

        $(this).off('load').on('load', function() {
            var r = $($(this).contents().get(0).getElementsByTagName('body')[0]).children('textarea');

            r.length == 0 ? UPLOADER._cbOnError(o.id) : UPLOADER._cbOnSuccess(o.id, r.val());

            $(this).remove();
        });

        form.submit();
    });

    $('body').append(iframe);
};

/**
 *
 * @param {object} o
 * @private
 */
UPLOADER._sendFileUsingAJAX = function(o) {
    var form = new FormData();

    form.append('file', o.object);

    $.ajax({
        url         : UPLOADER._uploaderSettings.uploaderAction,
        data        : form,
        processData : false,
        contentType : false,
        type        : 'POST'
    })
        .done(function(r) { UPLOADER._cbOnSuccess(o.id, r); })
        .fail(function( ) { UPLOADER._cbOnError  (o.id   ); });
};

/**
 *
 * @param {int} file_id
 * @param {string} data
 * @private
 */
UPLOADER._cbOnSuccess = function(file_id, data) {
    var o = UPLOADER._filesToUpload[file_id], r = jQuery.parseJSON(data);

    if (r != null) {
        o.status         = r.status;
        o.canBeUploaded  = ! (o.status == UPLOADER.STATUS_E_FILE_TOO_BIG || o.status == UPLOADER.STATUS_E_FILE_TYPE_NOT_ALLOWED || o.status == UPLOADER.STATUS_S_OK);
        o.object         = o.canBeUploaded ? o.object : null;
        o.serverResponse = r;
    } else {
        o.status         = UPLOADER.STATUS_E_ERROR;
        o.canBeUploaded  = true;
    }

    UPLOADER._callUploadedListeners(file_id);
};

/**
 *
 * @param {int} file_id
 * @private
 */
UPLOADER._cbOnError = function(file_id) {
    var o = UPLOADER._filesToUpload[file_id];

    o.status        = UPLOADER.STATUS_E_ERROR;
    o.canBeUploaded = true;

    UPLOADER._callUploadedListeners(file_id);
};

/**
 *
 * @param {int} file_id
 * @private
 */
UPLOADER._callUploadedListeners = function(file_id) {
    var o = UPLOADER._filesToUpload[file_id];

    if (UPLOADER._uploaderSettings.onFileUploaded) {
        UPLOADER._uploaderSettings.onFileUploaded(file_id, o.status);
    }

    if (--UPLOADER._nFilesInBatch == 0 && UPLOADER._uploaderSettings.onBatchUploaded) {
        UPLOADER._uploaderSettings.onBatchUploaded();
    }
};

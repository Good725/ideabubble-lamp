<?php
$found = false;
$selected_preset = false;

// Check for ID match
for ($i = 0; $i < count($presets) && !$found; $i++) {
    if ($presetId == $presets[$i]['id']) {
        $selected_preset = $presets[$i];
        $found = true;
    }
}

// If the supplied preset does not match an ID, check if it matches directory names
$matched_presets = [];
for ($i = 0; $i < count($presets) && !$found; $i++) {
    if ($presetId == $presets[$i]['directory']) {
        $matched_presets[] = $presets[$i];
    }
}

// If any of the directories were a match, use the one whose name matches as the main one.
// Otherwise, use the first one.
if (count($matched_presets)) {
    $selected_preset = $matched_presets[0];

    for ($i = 0; $i < count($matched_presets) && !$found; $i++) {
        if (strtolower($presetId) == strtolower($matched_presets[$i]['title'])) {
            $selected_preset = $matched_presets[$i];
            $found = true;
        }
    }
    $found = true;
}


// If the specified preset does not match an ID or directory name, check if it matches the preset name
for ($i = 0; $i < count($presets) && !$found; $i++) {
    if ($presetId == $presets[$i]['title']) {
        $selected_preset = $presets[$i];
        $found = true;
    }
}

if ($found && empty($matched_presets)) {
    $matched_presets[] = $selected_preset;
}

$img = imagecreatefromstring(file_get_contents($image));
$real_width = imagesx($img);
$real_height = imagesy($img);
$real_aspect_ratio = $real_width / $real_height;
imagedestroy($img);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Image Editor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('media') ?>/js/cropperjs/dist/cropper.css"/>
    <style>
        /* Basic */

        body {
            margin: 0;
            overflow-x: hidden;
        }

        .browserupgrade {
            margin: 0;
            padding: 5px 15px;
            background-color: #ff851b;
            color: #fff;
            text-align: center;
        }


        /* Header */

        .docs-header {
            margin-bottom: 0;
        }

        .navbar-toggle:hover,
        .navbar-toggle:focus {
            border-color: #0074d9;
        }

        .navbar-toggle .icon-bar {
            background-color: #0074d9;
        }


        /* Jumbotron */

        .docs-jumbotron {
            background-color: #0074d9;
            color: #fff;
        }

        .docs-jumbotron .version {
            font-size: 14px;
            color: #fff;
            filter: alpha(opacity=50);
            opacity: 0.5;
        }

        .docs-carbonads-container {
            position: relative;
        }

        .docs-carbonads {
            max-width: 350px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow: hidden;
        }

        .carbon-wrap {
            overflow: hidden;
        }

        .carbon-img {
            clear: left;
            float: left;
            display: block;
        }

        .carbon-text,
        .carbon-poweredby {
            display: block;
            margin-left: 140px;
        }

        .carbon-text,
        .carbon-text:hover,
        .carbon-text:focus {
            color: #fff;
            text-decoration: none;
        }

        .carbon-poweredby,
        .carbon-poweredby:hover,
        .carbon-poweredby:focus {
            color: #ddd;
            text-decoration: none;
        }

        @media (min-width: 992px) {
            .docs-carbonads {
                position: absolute;
                right: 0;
                bottom: 5px;
            }
        }


        /* Content */

        .img-container,
        .img-preview {
            background-color: #f7f7f7;
            width: 100%;
            text-align: center;
        }

        .img-container {
            min-height: 200px;
            max-height: 516px;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .img-container {
                min-height: 516px;
            }
        }

        .img-container > img {
            max-width: 100%;
        }

        .docs-preview {
            margin-right: -15px;
        }

        .img-preview {
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .img-preview > img {
            max-width: 100%;
        }

        .preview-lg {
            width: 263px;
            height: 148px;
        }

        .preview-md {
            width: 139px;
            height: 78px;
        }

        .preview-sm {
            width: 69px;
            height: 39px;
        }

        .preview-xs {
            width: 35px;
            height: 20px;
            margin-right: 0;
        }

        .docs-data > .input-group {
            margin-bottom: 10px;
        }

        .docs-data > .input-group > label {
            min-width: 80px;
        }

        .docs-data > .input-group > span {
            min-width: 50px;
        }

        .docs-buttons > .btn,
        .docs-buttons > .btn-group,
        .docs-buttons > .form-control {
            margin-right: 5px;
            margin-bottom: 10px;
        }

        .docs-toggles > .btn,
        .docs-toggles > .btn-group,
        .docs-toggles > .dropdown {
            margin-bottom: 10px;
        }

        .docs-tooltip {
            display: block;
            margin: -6px -12px;
            padding: 6px 12px;
        }

        .docs-tooltip > .icon {
            margin: 0 -3px;
            vertical-align: top;
        }

        .tooltip-inner {
            white-space: normal;
        }

        .btn-upload .tooltip-inner {
            white-space: nowrap;
        }

        @media (max-width: 400px) {
            .btn-group-crop {
                margin-right: -15px!important;
            }

            .btn-group-crop > .btn {
                padding-left: 5px;
                padding-right: 5px;
            }

            .btn-group-crop .docs-tooltip {
                margin-left: -5px;
                margin-right: -5px;
                padding-left: 5px;
                padding-right: 5px;
            }
        }

        .docs-options .dropdown-menu {
            width: 100%;
        }

        .docs-options .dropdown-menu > li {
            padding: 3px 20px;
        }

        .docs-options .dropdown-menu > li:hover {
            background-color: #f7f7f7;
        }

        .docs-options .dropdown-menu > li > label {
            display: block;
        }

        .docs-cropped .modal-body {
            text-align: center;
        }

        .docs-cropped .modal-body > img,
        .docs-cropped .modal-body > canvas {
            max-width: 100%;
        }


        /* Footer */

        .docs-footer {
            overflow: hidden;
        }

        .links {
            text-align: center;
            margin-bottom: 30px;
        }

        .heart {
            position: relative;
            display: block;
            width: 100%;
            height: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
            color: #ddd;
            font-size: 18px;
            line-height: 30px;
            text-align: center;
        }

        .heart:hover {
            color: #ff4136;
        }

        .heart:before {
            position: absolute;
            top: 50%;
            right: 0;
            left: 0;
            display: block;
            height: 0;
            border-top: 1px solid #eee;
            content: " ";
        }

        .heart:after {
            position: relative;
            z-index: 1;
            padding-left: 8px;
            padding-right: 8px;
            background-color: #fff;
            content: "â™¥";
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Content -->
<div>
    <div class="row">
        <div class="col-sm-12 form-group">
			<?php $logged_in_user = Auth::instance()->get_user(); ?>
            <input type="hidden" name="imageSource" value="<?=$image?>" />
			<?php // If the image is an avatar, its name must be the user's ID ?>
			<input type="text" name="image" value="<?= ($selected_preset['directory'] == 'avatars') ? $logged_in_user['id'].'.'.pathinfo($image, PATHINFO_EXTENSION) :  urldecode(basename($image));?>" class="form-control"<?= ($selected_preset['directory'] == 'avatars') ? ' readonly' : '' ?> />
		</div>
        <div class="col-sm-12 col-md-9">
            <div class="img-container" style="height: 500px;">
                <img src="<?=$image?>" alt="Picture">
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <!-- <h3 class="page-header">Preview:</h3> -->
            <div class="docs-preview clearfix">
                <div class="img-preview preview-lg"></div>
                <div class="img-preview preview-md"></div>
                <div class="img-preview preview-sm"></div>
                <div class="img-preview preview-xs"></div>
            </div>

            <!-- <h3 class="page-header">Data:</h3> -->
            <div class="docs-data" style="display: none;"> <!-- for debug -->
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataX">X</label>
                    <input type="text" class="form-control" id="dataX" placeholder="x">
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataY">Y</label>
                    <input type="text" class="form-control" id="dataY" placeholder="y">
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataWidth">Width</label>
                    <input type="text" class="form-control" id="dataWidth" placeholder="width"
						   value="<?= isset($selected_preset['width_large']) ? $selected_preset['width_large'] : ''?> " />
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataHeight">Height</label>
                    <input type="text" class="form-control" id="dataHeight" placeholder="height"
						   value="<?= isset($selected_preset['height_large']) ? $selected_preset['height_large'] : ''?> " />
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataRotate">Rotate</label>
                    <input type="text" class="form-control" id="dataRotate" placeholder="rotate">
                    <span class="input-group-addon">deg</span>
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataScaleX">ScaleX</label>
                    <input type="text" class="form-control" id="dataScaleX" placeholder="scaleX">
                </div>
                <div class="input-group input-group-sm">
                    <label class="input-group-addon" for="dataScaleY">ScaleY</label>
                    <input type="text" class="form-control" id="dataScaleY" placeholder="scaleY">
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="ratio-options">
        <div class="form-group">
            <label class="col-sm-2" for="preset_id">Preset</label>
            <div class="col-sm-4">
                <?php
                // If the selector is locked...
                // ... lock the entire dropdown, if there is only one allowed preset.
                // ... lock the disallowed options, if there is more than one allowed preset.
                $lock_dropdown = ((!empty($lockPreset) || !empty($_GET['lock_preset'])) && count($matched_presets) <= 1);
                $lock_options  = ((!empty($lockPreset) || !empty($_GET['lock_preset'])) && count($matched_presets)  > 1);

                $matched_preset_ids = array_column($matched_presets, 'id');
                ?>
                
                <select name="preset_id" class="form-control" id="preset_id" onchange="setPreset()"<?= $lock_dropdown ? ' disabled="disabled"' : '' ?>>
                    <option value=""<?= $lock_options ? ' disabled="disabled"' : '' ?>>Custom</option>
                    <?php foreach ($presets as $preset): ?>
                        <option
                            value="<?= $preset['id'] ?>"
                            data-directory="<?= $preset['directory']    ?>"
                            data-width="<?=     $preset['width_large']  ?>"
                            data-height="<?=    $preset['height_large'] ?>"
                            <?= ($preset['id'] == $selected_preset['id'])  ? ' selected="selected"' : '' ?>
                            <?= ($lock_options && !in_array($preset['id'], $matched_preset_ids)) ? ' disabled="disabled"' : '' ?>
                        ><?= htmlspecialchars($preset['title'] . ' (' . $preset['width_large'] . 'x' . $preset['height_large'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
		<div class="form-group">
			<label class="col-sm-2">Dimensions: </label>
			<span class="col-sm-10">
				<span id="preset-width"><?=  isset($selected_preset['width_large'])  ? $selected_preset['width_large']  : '' ?></span>
				&times;
				<span id="preset-height"><?= isset($selected_preset['height_large']) ? $selected_preset['height_large'] : '' ?></span>
			</span>

		</div>
        <!-- <button type="button" onclick="save();">Save</button> -->
    </div>
    <div class="row" id="actions">
        <div class="col-md-9 docs-buttons">
            <!-- <h3 class="page-header">Toolbar:</h3> -->
            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="move" title="Move">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;move&quot;)">
              <span class="fa fa-arrows"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
              <span class="fa fa-crop"></span>
            </span>
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
              <span class="fa fa-search-plus"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
              <span class="fa fa-search-minus"></span>
            </span>
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(-10, 0)">
              <span class="fa fa-arrow-left"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="move" data-option="10" data-second-option="0" title="Move Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(10, 0)">
              <span class="fa fa-arrow-right"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, -10)">
              <span class="fa fa-arrow-up"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="10" title="Move Down">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, 10)">
              <span class="fa fa-arrow-down"></span>
            </span>
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Rotate Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-45)">
              <span class="fa fa-rotate-left"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Rotate Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(45)">
              <span class="fa fa-rotate-right"></span>
            </span>
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
              <span class="fa fa-arrows-h"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
              <span class="fa fa-arrows-v"></span>
            </span>
                </button>
            </div>


            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="crop" title="Crop">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.crop()">
              <span class="fa fa-check"></span>
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="clear" title="Clear">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.clear()">
              <span class="fa fa-remove"></span>
            </span>
                </button>
            </div>

            <div class="btn-group" style="display: none;">
                <button type="button" class="btn btn-primary" data-method="disable" title="Disable">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.disable()">
                      <span class="fa fa-lock"></span>
                    </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="enable" title="Enable">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.enable()">
                      <span class="fa fa-unlock"></span>
                    </span>
                </button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
                      <span class="fa fa-refresh"></span>
                    </span>
                </button>

            </div>
            <div style="display: none;">
                <label class="btn btn-primary btn-upload" for="inputImage" title="Upload image file">
                    <input type="file" class="sr-only" id="inputImage" name="file" accept="image/*">
                    <span class="docs-tooltip" data-toggle="tooltip" title="Import image with Blob URLs">
                      <span class="fa fa-upload"></span>
                    </span>
                </label>
                <button type="button" class="btn btn-primary" data-method="destroy" title="Destroy" style="display: none;">
                    <span class="docs-tooltip" data-toggle="tooltip" title="cropper.destroy()">
                      <span class="fa fa-power-off"></span>
                    </span>
                </button>
            </div>

            <div class="btn-group btn-group-crop" style="display: none;">
                <button type="button" class="btn btn-primary" data-method="getCroppedCanvas">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getCroppedCanvas()">
              Get Cropped Canvas
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 160, &quot;height&quot;: 90 }">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getCroppedCanvas({ width: 160, height: 90 })">
              160&times;90
            </span>
                </button>
                <button type="button" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 320, &quot;height&quot;: 180 }">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getCroppedCanvas({ width: 320, height: 180 })">
              320&times;180
            </span>
                </button>
            </div>

            <!-- Show the cropped image in modal -->
            <div class="modal fade docs-cropped" id="getCroppedCanvasModal" role="dialog" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="getCroppedCanvasTitle">Cropped</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <a class="btn btn-primary" id="download" href="javascript:void(0);" download="cropped.png">Download</a>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal -->

            <button type="button" class="btn btn-primary" data-method="getData" data-option data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getData()">
            Get Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="setData" data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setData(data)">
            Set Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="getContainerData" data-option data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getContainerData()">
            Get Container Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="getImageData" data-option data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getImageData()">
            Get Image Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="getCanvasData" data-option data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getCanvasData()">
            Get Canvas Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="setCanvasData" data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setCanvasData(data)">
            Set Canvas Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="getCropBoxData" data-option data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.getCropBoxData()">
            Get Crop Box Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="setCropBoxData" data-target="#putData" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setCropBoxData(data)">
            Set Crop Box Data
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="moveTo" data-option="0" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.moveTo(0)">
            0,0
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="zoomTo" data-option="1" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoomTo(1)">
            100%
          </span>
            </button>
            <button type="button" class="btn btn-primary" data-method="rotateTo" data-option="180" style="display: none;">
          <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotateTo(180)">
            180°
          </span>
            </button>
            <input type="text" class="form-control" id="putData" placeholder="Get data to here or set data with this value" style="display: none;">

        </div><!-- /.docs-buttons -->

        <div class="col-md-3 docs-toggles" style="display: none;">
            <!-- <h3 class="page-header">Toggles:</h3> -->
            <div class="btn-group btn-group-justified" data-toggle="buttons">
                <label class="btn btn-primary active">
                    <input type="radio" class="sr-only" id="aspectRatio1" name="aspectRatio" value="1.7777777777777777">
            <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 16 / 9">
              16:9
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1.3333333333333333">
            <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 4 / 3">
              4:3
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="aspectRatio3" name="aspectRatio" value="1">
            <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 1 / 1">
              1:1
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="aspectRatio4" name="aspectRatio" value="0.6666666666666666">
            <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 2 / 3">
              2:3
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="aspectRatio5" name="aspectRatio" value="NaN">
            <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: NaN">
              Free
            </span>
                </label>
            </div>

            <div class="btn-group btn-group-justified" data-toggle="buttons">
                <label class="btn btn-primary active">
                    <input type="radio" class="sr-only" id="viewMode0" name="viewMode" value="0" checked>
            <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 0">
              VM0
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="viewMode1" name="viewMode" value="1">
            <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 1">
              VM1
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="viewMode2" name="viewMode" value="2">
            <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 2">
              VM2
            </span>
                </label>
                <label class="btn btn-primary">
                    <input type="radio" class="sr-only" id="viewMode3" name="viewMode" value="3">
            <span class="docs-tooltip" data-toggle="tooltip" title="View Mode 3">
              VM3
            </span>
                </label>
            </div>

            <div class="dropdown dropup docs-options" style="display: none;">
                <button type="button" class="btn btn-primary btn-block dropdown-toggle" id="toggleOptions" data-toggle="dropdown" aria-expanded="true">
                    Toggle Options
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="toggleOptions">
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="responsive" checked>
                            responsive
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="restore" checked>
                            restore
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="checkCrossOrigin" checked>
                            checkCrossOrigin
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="checkOrientation" checked>
                            checkOrientation
                        </label>
                    </li>

                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="modal" checked>
                            modal
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="guides" checked>
                            guides
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="center" checked>
                            center
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="highlight" checked>
                            highlight
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="background" checked>
                            background
                        </label>
                    </li>

                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="autoCrop" checked>
                            autoCrop
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="movable" checked>
                            movable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="rotatable" checked>
                            rotatable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="scalable" checked>
                            scalable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="zoomable" checked>
                            zoomable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="zoomOnTouch" checked>
                            zoomOnTouch
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="zoomOnWheel" checked>
                            zoomOnWheel
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="cropBoxMovable" checked>
                            cropBoxMovable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="cropBoxResizable" checked>
                            cropBoxResizable
                        </label>
                    </li>
                    <li role="presentation">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="toggleDragModeOnDblclick" checked>
                            toggleDragModeOnDblclick
                        </label>
                    </li>
                </ul>
            </div><!-- /.dropdown -->

            <a class="btn btn-default btn-block" data-toggle="tooltip" href="https://fengyuanchen.github.io/cropper" title="Cropper as jQuery plugin" style="display: none;">Cropper</a>

        </div><!-- /.docs-toggles -->
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="<?= URL::get_engine_plugin_assets_base('media') ?>/js/cropperjs/dist/cropper.js"></script>

<script>
    var imageOptions = {};
    var cropper = null;
    var image = null;
    var cropParams = null;
    window.onload = function () {

        'use strict';

        var Cropper = window.Cropper;
        var console = window.console || { log: function () {} };
        var container = document.querySelector('.img-container');
        image = container.getElementsByTagName('img').item(0);
        var download = document.getElementById('download');
        var actions = document.getElementById('actions');
        var dataX = document.getElementById('dataX');
        var dataY = document.getElementById('dataY');
        var dataHeight = document.getElementById('dataHeight');
        var dataWidth = document.getElementById('dataWidth');
        var dataRotate = 0;
        var dataScaleX = document.getElementById('dataScaleX');
        var dataScaleY = document.getElementById('dataScaleY');
        var options = {
            aspectRatio: <?php
            if ($selected_preset) {
                if ($selected_preset['width_large'] > 0 && $selected_preset['height_large'] > 0) {
                    echo $selected_preset['width_large'] / $selected_preset['height_large'];
                } else {
                    echo $real_aspect_ratio;
                }
            } else {
                echo 'NaN';
            }
            ?>,
			<?php if ($selected_preset): ?>
			data: {
				width: <?= $selected_preset['width_large'] == 0 ? $real_width : $selected_preset['width_large'] ?>,
				height: <?= $selected_preset['height_large'] == 0 ? $real_height : $selected_preset['height_large'] ?>,
				x: 0,
				y: 0
			},
			<?php endif; ?>
            preview: '.img-preview',
            build: function () {
                console.log('build');
            },
            built: function () {
                if (cropParams != null) {
                    //cropper.setCropBoxData(cropParams);
                }
                /*
                 * some workaround for duplicate cropper issue:ENGINE-394
                 * */
                if ($(".cropper-container").length > 1) {
                    for (var i = $(".cropper-container").length - 1 ; i > 0 ; --i) {
                        $($(".cropper-container")[i]).remove();
                    }
                }
                console.log('built');
            },
            cropstart: function (data) {
                console.log('cropstart', data.action);
            },
            cropmove: function (data) {
                console.log('cropmove', data.action);
            },
            cropend: function (data) {
                console.log('cropend', data.action);
            },
            crop: function (data) {
                if (data.isTrusted == false) { // some strange issue
                    data = cropper.getData();
                }
                console.log('crop:'+JSON.stringify(data));
                dataX.value = Math.round(data.x);
                dataY.value = Math.round(data.y);
                dataHeight.value = Math.round(data.height);
                dataWidth.value = Math.round(data.width);
                dataRotate = !isUndefined(data.rotate) ? data.rotate : '';
                dataScaleX.value = !isUndefined(data.scaleX) ? data.scaleX : '';
                dataScaleY.value = !isUndefined(data.scaleY) ? data.scaleY : '';
                $("#preset-width").html(parseInt(data.width) + "px");
                $("#preset-height").html(parseInt(data.height) + "px");
            },
            zoom: function (data) {
                console.log('zoom', data.ratio);
            }
        };
        imageOptions = options;
        cropper = new Cropper(image, options);

        function isUndefined(obj) {
            return typeof obj === 'undefined';
        }

        function preventDefault(e) {
            if (e) {
                if (e.preventDefault) {
                    e.preventDefault();
                } else {
                    e.returnValue = false;
                }
            }
        }


        // Tooltip
        $('[data-toggle="tooltip"]').tooltip();


        // Buttons
        if (!document.createElement('canvas').getContext) {
            $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
        }

        if (typeof document.createElement('cropper').style.transition === 'undefined') {
            $('button[data-method="rotate"]').prop('disabled', true);
            $('button[data-method="scale"]').prop('disabled', true);
        }


        // Download
        if (typeof download.download === 'undefined') {
            download.className += ' disabled';
        }


        // Options
        actions.querySelector('.docs-toggles').onclick = function (event) {
            var e = event || window.event;
            var target = e.target || e.srcElement;
            var cropBoxData;
            var canvasData;
            var isCheckbox;
            var isRadio;

            if (!cropper) {
                return;
            }

            if (target.tagName.toLowerCase() === 'span') {
                target = target.parentNode;
            }

            if (target.tagName.toLowerCase() === 'label') {
                target = target.getElementsByTagName('input').item(0);
            }

            isCheckbox = target.type === 'checkbox';
            isRadio = target.type === 'radio';

            if (isCheckbox || isRadio) {
                if (isCheckbox) {
                    options[target.name] = target.checked;
                    cropBoxData = cropper.getCropBoxData();
                    canvasData = cropper.getCanvasData();

                    options.built = function () {
                        console.log('built');
                        cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
                    };
                } else {
                    options[target.name] = target.value;
                    options.built = function () {
                        console.log('built');
                    };
                }

                // Restart
                cropper.destroy();
                cropper = new Cropper(image, options);
            }
        };


        // Methods
        actions.querySelector('.docs-buttons').onclick = function (event) {
            var e = event || window.event;
            var target = e.target || e.srcElement;
            var result;
            var input;
            var data;

            if (!cropper) {
                return;
            }

            while (target !== this) {
                if (target.getAttribute('data-method')) {
                    break;
                }

                target = target.parentNode;
            }

            if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
                return;
            }

            data = {
                method: target.getAttribute('data-method'),
                target: target.getAttribute('data-target'),
                option: target.getAttribute('data-option'),
                secondOption: target.getAttribute('data-second-option')
            };

            if (data.method) {
                if (typeof data.target !== 'undefined') {
                    input = document.querySelector(data.target);

                    if (!target.hasAttribute('data-option') && data.target && input) {
                        try {
                            data.option = JSON.parse(input.value);
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                if (data.method === 'getCroppedCanvas') {
                    data.option = JSON.parse(data.option);
                }

                result = cropper[data.method](data.option, data.secondOption);

                switch (data.method) {
                    case 'scaleX':
                    case 'scaleY':
                        target.setAttribute('data-option', -data.option);
                        break;

                    case 'getCroppedCanvas':
                        if (result) {

                            // Bootstrap's Modal
                            $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

                            if (!download.disabled) {
                                download.href = result.toDataURL();
                            }
                        }

                        break;

                    case 'destroy':
                        cropper = null;
                        break;
                }

                if (typeof result === 'object' && result !== cropper && input) {
                    try {
                        input.value = JSON.stringify(result);
                    } catch (e) {
                        console.log(e.message);
                    }
                }

            }
        };

        document.body.onkeydown = function (event) {
            var e = event || window.event;

            if (!cropper || this.scrollTop > 300) {
                return;
            }

            switch (e.charCode || e.keyCode) {
                case 37:
                    preventDefault(e);
                    cropper.move(-1, 0);
                    break;

                case 38:
                    preventDefault(e);
                    cropper.move(0, -1);
                    break;

                case 39:
                    preventDefault(e);
                    cropper.move(1, 0);
                    break;

                case 40:
                    preventDefault(e);
                    cropper.move(0, 1);
                    break;
            }
        };


        // Import image
        var inputImage = document.getElementById('inputImage');
        var URL = window.URL || window.webkitURL;
        var blobURL;

        if (URL) {
            inputImage.onchange = function () {
                var files = this.files;
                var file;

                if (cropper && files && files.length) {
                    file = files[0];

                    if (/^image\/\w+/.test(file.type)) {
                        blobURL = URL.createObjectURL(file);
                        cropper.reset().replace(blobURL);
                        inputImage.value = null;
                    } else {
                        window.alert('Please choose an image file.');
                    }
                }
            };
        } else {
            inputImage.disabled = true;
            inputImage.parentNode.className += ' disabled';
        }
		$('#preset_id').trigger('change');
		setTimeout(setPreset,.1);
    };

    function setPreset()
    {
        var timer = setInterval(function()
        {
            // Don't continue, until the image has loaded
            if (cropper.ready)
            {
                clearInterval(timer);

                var select = document.getElementById('preset_id');
                var preset = "";
                var presetId = 0;
                var width = 0;
                var height = 0;
                var ratio = 1;
                var imgData = cropper.getImageData();
                cropParams = cropper.getCropBoxData();
                var canvasParams = cropper.getCanvasData();
                var dratio = canvasParams.width / canvasParams.naturalWidth;

                if (select.selectedIndex < 1) {
                    preset = "Custom";
                    width = "custom";
                    height = "custom";
                    ratio = NaN;
                    $("#preset-width").html(parseInt(cropParams.width) + "px");
                    $("#preset-height").html(parseInt(cropParams.height) + "px");
                } else {
                    var option = select.options[select.selectedIndex];
                    preset = option.innerHTML;
                    var data_width  = option.getAttribute('data-width');
                    var data_height = option.getAttribute('data-height');

                    // Scale the cropper, so that it is big enough to encapsulate the image.
                    width  = parseInt(imgData.width);
                    height = parseInt(data_height * canvasParams.width / data_width);

                    // Scale the cropper, so that it fits inside the image
                    if (imgData.height < height)
                    {
                        height = parseInt(imgData.height);
                        width = parseInt(height * data_width / data_height);
                    }

                    dratio = 1;
                    var left = canvasParams.left - (width - canvasParams.width) / 2;

                    if (width == 0) {
                        width = "auto";
                        cropParams.width = canvasParams.naturalWidth;
                        cropParams.height = height;
                        ratio = NaN;
                    } else if (height == 0) {
                        cropParams.height = canvasParams.naturalHeight;
                        cropParams.width = width;
                        height = "auto";
                        ratio = NaN;
                    } else {
                        ratio = width / height;
                        cropParams.width = width;
                        cropParams.height = height;
                    }
                    $("#preset-width").html(width + "px");
                    $("#preset-height").html(height + "px");
                }

                cropParams.height *= dratio;
                cropParams.width *= dratio;
                cropParams.left = (typeof left != 'undefined') ? left : canvasParams.left;
                cropParams.top = canvasParams.top;

                /*if (cropParams.width > canvasParams.width) {
                    var fratio = cropParams.width / canvasParams.width;
                    cropParams.width = canvasParams.width;
                    cropParams.height /= fratio;
                    cropParams.left = canvasParams.left;
                }
                if (cropParams.height > canvasParams.height) {
                    var fratio = cropParams.height / canvasParams.height;
                    cropParams.height = canvasParams.height;
                    cropParams.width /= fratio;
                    cropParams.top = canvasParams.top;
                }*/
                console.log(width+"x"+height+" => "+cropParams.width+"x"+cropParams.height);

                imageOptions.aspectRatio = ratio;
                cropper.destroy();
                cropper = new Cropper(image, imageOptions);
                //setTimeout(function(){
                    cropper.setCropBoxData(cropParams);
                //}, 1000)


            }
        }, 100);


    }

    function save()
    {
        //var imageData = cropper.getCroppedCanvas().toDataURL();
        var cropBox = cropper.getCropBoxData();
        var canvasBox = cropper.getCanvasData();
        var data = cropper.getData();
        var container = cropper.getContainerData();
        var displayScaledRatio = canvasBox.width / canvasBox.naturalWidth;
        console.log(cropBox);
        console.log(canvasBox);
        console.log(data);
        var filename = $("[name=image]").val();
        var presetId = $("#preset_id").val();
        var params = {
            //imageData: imageData,
            filename: filename,
            presetId: presetId,
            data: {
                selector_h: parseInt(cropBox.height / displayScaledRatio),
                selector_w: parseInt(cropBox.width / displayScaledRatio)
            },
            selectorX: parseInt(cropBox.left / displayScaledRatio),
            selectorY: parseInt(cropBox.top / displayScaledRatio),
            imageW: parseInt(canvasBox.width / displayScaledRatio),
            imageH: parseInt(canvasBox.height / displayScaledRatio),
            imageX: parseInt(canvasBox.left / displayScaledRatio),
            imageY: parseInt(canvasBox.top / displayScaledRatio),
            imageSource: $("[name=imageSource]").val(),
            imageRotate: data.rotate,
            viewPortW: parseInt(container.width / displayScaledRatio),
            viewPortH: parseInt(container.height / displayScaledRatio)
        };

        $.post(
            "/admin/media/ajax_cropzoom_upload?filename=" + encodeURIComponent(params.filename) + "&preset_id=" + params.presetId + '&json=true',
            params,
            function (response){
                console.log(response);
                var msg = {};
                msg.saved = true;
                msg.image = response;
                window.top.postMessage(JSON.stringify(msg), "*");
                //window.top.location.reload();
            }
        );
    }

    function msglisten(event)
    {
        try {
            var data = JSON.parse(event.data);
            if (data.save) {
                save();
            }
        } catch (exc) {

        }
    }

    window.addEventListener("message", msglisten, false);

    //window.top.$("#image-edit-save").on("click", save);
</script>
</body>
</html>

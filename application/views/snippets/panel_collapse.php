<?php
/* A collapsible panel
 *

    echo View::factory('snippets/panel_collapse')->set([
        'type'      => '',
        'class'     => '',
        'id'        => '',
        'collapsed' => false
        'title'     => '',
        'subtitle'  => false
        'body'      => ''
    ]);
 *
 * type      - The type used in the bootstrap contextual class, `default` (default), `primary`, `success`, `info`, `warning`, `danger`
 * class     - Custom classes to add to the panel
 * id        - Unique identifier for the panel. IDs attributes within the panel will also be prefixed with this.
 * collapsed - Specify if the panel is to be collapsed or not by default; `true`, `false` (default)
 * title     - Text to appear in the panel title
 * subtitle  - Subtitle to appear below the title. Enter the title or set to `true` to add the empty HTML or `false` (default) to exclude the HTML
 * body      - The HTML to appear within the panel
 */
?>
<?php
$collapsed = isset($collapsed) ? $collapsed : false;

$classes  = 'panel';
$classes .= !empty($type) ? ' panel-'.$type : ' panel-default ';
$classes .= isset($subtitle) && $subtitle !== true ? ' panel-has_subtitle' : '';
$classes .= isset($class) ? ' '.$class : ''
?>

<div class="<?= $classes ?>" id="<?= $id ?>">
    <div
        class="panel-heading"
        data-toggle="collapse"
        data-target="#<?= $id ?>-body"
        aria-expanded="<?= $collapsed ? 'false' : 'true' ?>"
    >
        <div class="right">
            <button type="button" class="button--plain expanded-invert">
                <span class="icon-angle-up"></span>
            </button>

            <?php if (!empty($removable)): ?>
                <button type="button" class="btn-link panel-remove ml-2">
                    <span class="fa icon-times" aria-hidden="true"></span>
                </button>
            <?php endif; ?>
        </div>

        <h3 class="panel-title">
            <?
            if (isset($title['html'])) {
                echo $title['html'];
            } else {
                echo htmlspecialchars($title);
            }
            ?>

            <?php if (!empty($subtitle)): ?>
                <strong class="panel-subtitle" id="<?= $id ?>-subtitle">
                    <?= $subtitle === true ? '' : $subtitle?>
                </strong>
            <?php endif; ?>
        </h3>
    </div>

    <div class="panel-body collapse <?= $collapsed ? '' : 'in' ?>" id="<?= $id ?>-body">
        <?= $body ?>
    </div>
</div>

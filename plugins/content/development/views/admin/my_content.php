<style>
    .my_content-sidebar-header {
        border: solid #eee;
        border-width: 1px 1px 0;
        padding: .5rem .5rem .5rem 1rem;
    }
    a.panel-heading {
        display: block;
        text-decoration: none;
    }

    .my_content-sidebar .panel {
        margin-bottom: 0;
        border-radius: 0;
    }

    .my_content-sidebar .panel + .panel {
        border-top: 0;
    }

    .my_content-sidebar .panel-heading[aria-expanded="false"] {
        border-bottom: 0;
    }

    .my_content-sidebar .form-checkbox {
        font-size: .75em;
    }

    dl.dl-flex {
        display: flex;
        flex-wrap: wrap;
    }

    .dl-flex > dt {
        margin-bottom: .5em;
        width: 25%;
    }

    .dl-flex > dd {
        margin-bottom: .5em;
        width: 75%;
    }

    .my_content-lesson {
        position: relative;
    }

    .my_content-lesson_content ul {
        margin-left: 1.5em;
    }

    .my_content-lesson-check-wrapper {
        position: absolute;
        top: .125em;
        left: .6em;
    }

    .my_content-lesson-type-toggle {
        color: inherit;
        padding: .25em .1em .25em 2em;
        text-align: left;
        text-decoration: none;
        width: 100%;
    }

    .my_content-lesson-type-toggle:hover {
        text-decoration: none;
    }

    .my_content-lesson-complete:disabled + .form-checkbox-helper,
    .my_content-lesson-type-toggle:disabled {
        opacity: .5;
    }

    .my_content-lesson_content iframe {
        width: 100%;
        min-height: 300px;
    }

    #my_content-lesson-vimeo .plyr__video-embed__container {
        transform: none !important; /* Need to debug why this is being miscalculated */
    }

    .my_content-sidebar-toggle-show {
        backgroud-color: #000;
        background-color: #000c;
        border: none;
        color: #fff;
        overflow: hidden;
        padding: .5em 1em;
        position: absolute;
        right: 15px;
        white-space: nowrap;
        width: 170px;
        height: 50px;
        z-index: 1;
    }

    .my_content-sidebar-toggle-show:hover {
        transition: width .5s;
    }

    .my_content-sidebar-toggle-show:not(:hover) {
        width: 50px;
        min-width: 50px;
    }

    .my_content-sidebar-toggle-show:not(:hover) .my_content-sidebar-toggle-show-text {
        display: none;
    }
</style>

<input type="hidden" id="content_id" value="<?= isset($content->id) ? $content->id : '' ?>" />
<input type="hidden" id="content-allow_skipping" value="<?= isset($content) ? $content->allow_skipping : '' ?>" />

<div class="row gutters">
    <div class="col-sm-8" id="my_content-column-content">
        <h2 id="mycontent-lesson-title"> <?= isset($sections[$open_section]) ? htmlentities($sections[$open_section]['title']) : '' ?></h2>

        <button type="button" class="my_content-sidebar-toggle-show hidden" id="my_content-sidebar-toggle-show">
            <span class="flaticon-back"></span>
            <span class="my_content-sidebar-toggle-show-text"><?= $content->label ? $content->label : 'Section' ?> content</span>
        </button>

        <div class="my_content-lessons">
            <div class="my_content-lesson_content" id="my_content-lesson-text"></div>

            <div class="my_content-lesson-navigation text-center" id="my_content-lesson-progress">
                <div id="my_content-lesson-countdown"></div>

                <div class="form-group">
                    <div class="row gutters">
                        <div class="col-md-offset-3 col-md-6">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-lg" id="my_content-prev"><?= __('Prev') ?></button>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-lg btn-primary" id="my_content-next" disabled><?= __('Next') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4" id="my_content-column-sidebar">
        <div class="my_content-sidebar">
            <div class="my_content-sidebar-header">
                <h2><?= $content->label ? $content->label : 'Section' ?> content
                    <button type="button" class="button--plain p-0 right" id="my_content-sidebar-toggle-hide">
                        <span class="icon_close"></span>
                    </button>
                </h2>
            </div>

            <div class="my_content-sidebar-content clearfix">
                <?php $previous_subsection_complete = true; ?>
                <?php foreach ($content->get_ordered_children() as $section_number => $section): ?>
                    <div class="panel panel-default my_content-section">
                        <a class="panel-heading" data-toggle="collapse" data-target="#my_content-section-<?= 1+$section_number ?>-body" aria-expanded="<?= $section_number == $open_section ? 'true' : 'false' ?>">
                            <h3 class="panel-title">
                                <span class="expanded-invert icon-angle-down right"></span>
                                <span><?= $section_number+1 ?>: <?= htmlentities($section->name) ?></span>
                            </h3>

                            <?php $subsections = $section->get_ordered_children(); ?>

                            <div style="font-size:13px;">
                                <span class="my_content-section-complete_count"><?= $section->count_user_complete_children() ?></span> /
                                <span class="my-content-section-count_subsections"><?= count($subsections) ?></span> |
                                <?= $section->get_children_duration_formatted('medium') ?>
                            </div>
                        </a>

                        <div class="panel-body p-0 <?= $section_number == $open_section ? '' : 'collapse' ?> " id="my_content-section-<?= 1 + $section_number ?>-body">
                            <ul class="list-unstyled">
                                <?php foreach ($subsections as $subsection_number => $subsection): ?>
                                    <?php $is_complete = $subsection->is_complete_by_user();?>

                                    <li class="my_content-lesson">
                                        <div class="my_content-lesson-check-wrapper">
                                            <?php
                                            $attributes = [
                                                'class'           => 'my_content-lesson-complete',
                                                'readonly'        => (!$allow_skipping && $is_complete),
                                                'data-section'    => 1 + $section_number,
                                                'data-subsection' => 1 + $subsection_number,
                                                'data-content_id' => $subsection->id
                                            ];
                                            echo Form::ib_checkbox(null, null, null, $is_complete, $attributes);
                                            ?>
                                        </div>

                                        <button
                                            type="button" class="button--plain my_content-lesson-type-toggle"
                                            data-type="text"
                                            data-section="<?= 1+$section_number ?>"
                                            data-subsection="<?= 1+$subsection_number ?>"
                                            data-duration="<?= $subsection->duration ?>"
                                            data-content_id="<?= $subsection->id ?>"
                                            data-has_content="true"
                                            data-previous-subsection-complete="<?=$previous_subsection_complete?>"
                                            <?= !$previous_subsection_complete && !$allow_skipping && !$subsection->survey_id ? ' disabled="disabled"' : '' ?>
                                            >
                                            <strong style="display: block; font-weight: 500;">
                                                <?= 1+$subsection_number ?>.
                                                <span class="my_content-lesson-toggle-title"><?= htmlentities($subsection->name) ?></span>
                                            </strong>

                                            <span>
                                                <span class="icon-<?= $subsection->get_icon() ?>" style="color: #ccc;"></span>
                                                <?= $subsection->get_duration_formatted('medium') ?>
                                            </span>
                                        </button>
                                    </li>
                                    <?php $previous_subsection_complete = $is_complete; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

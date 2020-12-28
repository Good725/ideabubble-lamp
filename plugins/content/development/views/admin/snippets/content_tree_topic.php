<li class="row no-gutters vertically_center mt-2 content-topic" data-depth="<?= $depth ?>" data-id="<?= $topic->id ?>">
    <div class="col-xs-10" style="width: calc(100% - 2em);">
        <div class="border rounded p-2 pl-4 <?= $depth > 1 ? 'bg-light' : 'bg-white' ?>">
            <?php
            $subtopics = $topic->id ? $topic->children->order_by('order')->find_all_undeleted() : [];
            $amount = $subtopics ? $subtopics->count() : 0;
            ?>

            <div class="content-topic-heading row p-0 vertically_center" data-toggle="collapse" data-target="#content-tree-<?= $topic->id ?>-collapsible" aria-expanded="false">
                <h3 style="font-weight: 500;">
                    <span data-section_label="<?= !empty($label) ? $label : 'Section' ?>" class="content-topic-name" data-id="<?= $topic->id ?>">
                        <?= htmlentities($topic->name) ?>
                    </span>

                    <span class="content-tree-subtopic-counter" style="font-weight: 300;">
                        <span class="singular<?= $amount == 1 ? '' : ' hidden' ?>">
                            (1 sub topic)
                        </span>

                        <span class="plural<?= $amount == 1 ? ' hidden' : '' ?>">
                            (<span class="content-tree-subtopic-counter-amount"><?= $amount ?></span> sub topics)
                        </span>
                    </span>
                </h3>

                <div class="ml-auto">
                    <span class="content-topic-details edit_mode<?= $topic->has_content() ? '' : ' hidden' ?>" data-id="<?= $topic->id ?>">
                        <span class="icon-<?= $topic->get_icon() ?>" title="<?= htmlentities($topic->type->friendly_name) ?>"></span>

                        <span class="content-topic-duration d-inline-block text-right" style="min-width: 3em;">
                            <?= $topic->duration ? htmlentities($topic->get_duration_formatted('medium')) : '' ?>
                        </span>
                    </span>

                    <button
                        type="button" class="button--plain content-topic-add-modal-trigger"
                        data-toggle="modal" data-target="#content-add-modal" data-id="<?= $topic->id ?>" data-depth="<?= $depth ?>"
                        >

                        <span class="add_mode btn btn-lg btn-primary<?= $topic->has_content() ? ' hidden' : '' ?>">Add content</span>
                        <span class="edit_mode btn-lg btn-link px-1<?= $topic->has_content() ? '' : ' hidden' ?>">Edit content</span>
                    </button>

                    <button type="button" class="btn-link p-0">
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>

            <div class="content-topic-subtopics text-center collapse" id="content-tree-<?= $topic->id ?>-collapsible">
                <ul class="content-topics-list text-left">
                    <?php
                    foreach ($subtopics as $subtopic) {
                        echo View::factory('admin/snippets/content_tree_topic')
                            ->set('topic', $subtopic)
                            ->set('depth', $depth + 1)
                            ->render();
                    }
                    ?>
                </ul>

                <div class="content-topic-add-form row gutters my-2" style="padding-right: 2em;">
                    <input type="hidden" class="content-topic-add-parent_id" value="<?= $topic->id ?>">
                    <input type="hidden" class="content-topic-add-depth" value="<?= $depth ?>">

                    <div class="col-sm-4">
                        <?= Form::ib_input(null, null, null, ['class' => 'content-topic-add-name', 'placeholder' => 'Sub-topic name']) ?>
                    </div>

                    <div class="col-sm-4 right text-right">
                        <button type="button" class="content-topic-add-btn btn btn-lg btn-default">Add sub topic</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-1 text-center" style="width: 2em;">
        <button type="button" class="button--plain content-topic-remove text-decoration-none" data-id="<?= $topic->id ?>" data-toggle="modal" data-target="#content-delete-modal">
            <span class="icon_close" style="font-size: 1.5em;"></span>
        </button>
    </div>
</li>
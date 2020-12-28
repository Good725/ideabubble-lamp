<div id="booking_popup" class="booking_popup sectionOverlay" style="display: none;">
    <div class="overlayer"></div>

    <div class="screenTable">
        <div class="screenCell">
            <div class="sectioninner zoomIn small-width">
                <div class="popup-header">
                    <span class="popup-title"></span>

                    (ID: <span id="booking_popup-schedule_id"></span>)

                    <button type="button" class="button--plain basic_close">
                        <span class="fa fa-times" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="popup-content course-txt page-content">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?= URL::overload_asset('img/package1.jpg') ?>" id="booking_popup-image" alt="" />
                        </div>

                        <div class="col-md-8 booking_popup-topics" id="booking_popup-topics">
                            <h2><?= __('Topics') ?></h2>

                            <div class="topics-list" id="booking_popup-topics-list"></div>
                        </div>
                    </div>

                    <h2><?= __('Summary') ?></h2>

                    <div id="booking_popup-description"></div>
                </div>
            </div>
        </div>
    </div>
</div>

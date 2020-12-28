<?php
/*
$result can have the following keys:
- type          : course, event, page, etc.
- id
- link
- link_2        : Used, if there is a second link/button
- image         : image (the controller should ideally specify a placeholder image if the item has no image)
- image_overlay : text to appear over the image
- title
- subtitle
- currency
- price_amount  : price, excluding currency
- tags
- select_list
  - id
  - label
  - options
- summary
- button_text
- button_text_2
*/
?>

<div class="course-list-column">
    <form action="<?= $result['link'] ?>" method="get" class="course-widget" data-type="<?= $result['type'] ?>" data-id="<?= $result['id'] ?>">
        <button type="submit" class="button--plain course-widget-image" tabindex="-1">
            <img src="<?= $result['image'] ?>" alt="" />

            <?php if (!empty($result['image_overlay'])): ?>
                <div class="course-widget-location" data-location="<?= isset($result['image_overlay_name']) ? $result['image_overlay_name'] : $result['image_overlay'] ?>">
                    <?= htmlentities($result['image_overlay']) ?>
                </div>
            <?php endif; ?>

            <?php
            $currency_symbol   = isset($result['currency_symbol']) ? $result['currency_symbol'] : '&euro;';
            $has_initial_price = (!empty($result['price_amount']) && (float) $result['price_amount'] > 0);
            ?>

            <div class="course-widget-price grid_only"<?= $has_initial_price ? ' style="display: block;"' : '' ?>>
                <?= $result['same_fee'] ? __('Price') : __('From') ?>
                <?= $currency_symbol ?><span class="course-widget-price-amount"><?=
                    $has_initial_price ? ($result['price_amount'] == (int) $result['price_amount'] ? (int) $result['price_amount'] : number_format($result['price_amount'], 2)) : ''
                    ?></span>
            </div>
        </button>

        <div class="course-widget-header grid_only">
            <input type="hidden" name="id" value="<?= $result['id'] ?>">
            <h2 class="course-widget-title"><?= htmlentities($result['title']) ?></h2>
        </div>

        <div class="course-widget-details">
            <div class="course-widget-header list_only">
                <h2 class="course-widget-title"><?= htmlentities($result['title']) ?></h2>
            </div>

            <?php if (!empty($result['tags'])): ?>
                <div class="course-widget-location_and_tags">
                    <div class="course-widget-tags list_only">
                        <?php
                        foreach ($result['tags'] as $i => $tag) {
                            echo htmlentities($tag);
                            if ($i < count($result['tags'])) {
                                echo ' | ';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($result['subtitle']) && $result['subtitle'] !== false): ?>
                <div class="course-widget-level <?= isset($result['tags']) ? ' grid_only' : '' ?>"><?= $result['subtitle'] ?></div>
            <?php endif; ?>

            <div class="course-widget-time_and_date<?= $result['times_options'] ? ' course-widget-time_and_date--with_options' : '' ?>">
                <?php if ($result['times_options']): ?>
                    <?php $select_id = 'search_'.$result['type'].' '.$result['id'].'_times'; ?>

                    <label class="sr-only" for="<?= $select_id ?>"><?= __('Time and date')?></label>

                    <?php
                    // Has at least one value for "repeat" and no empty values for "repeat".
                    $is_repeating = (count(array_column($result['schedules'], 'repeat')) > 0 && !in_array(null, array_column($result['schedules'], 'repeat')));
                    $show_repeating_start_dates = ($is_repeating && Settings::instance()->get('show_start_date_for_repeating_timeslots'));
                    ?>

                    <select class="form-input course-widget-schedule<?= $show_repeating_start_dates ? ' pl-1' : '' ?>" name="schedule_id" id="<?= $select_id ?>">
                        <option value="">
                            <?php if ($show_repeating_start_dates): ?>
                                <?= __('Choose your start date') // Different text and padding to make more room for the text ?>
                            <?php else: ?>
                                <?= __('Choose your date') ?>
                            <?php endif; ?>
                        </option>
                        <?php foreach ($result['times_options'] as $option): ?>
                            <option<?= html::attributes($option['attributes']) ?>><?= $option['text'] ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($result['date_start']): ?>
                    <div class="course-widget-time_and_date-text">
                        <div>
                            <span class="nowrap"><?= date('F j, g:i a', strtotime($result['date_start'])) ?></span>
                            <?= ($result['date_end'] && $result['date_end'] != $result['date_start']) ?  ' &ndash; <span class="nowrap">'.date('F j, g:i a', strtotime($result['date_end'])).'</span>'  : '' ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($has_initial_price): ?>
                    <div class="course-widget-price list_only" style="display: block;">
                        <div class="course-widget-price-original">
                            <?= $currency_symbol ?><span class="course-widget-price-amount"><?= number_format($result['price_amount'], 2) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="course-widget-price list_only">
                        <div class="course-widget-price-original">
                            <s><?= $currency_symbol ?><span class="course-widget-price-amount">/span></s>
                        </div>

                        <div>
                            <span class="course-widget-price-discount_text"><?= __('Discounted to') ?></span>
                            <span class="course-widget-price-current">
                                <?= $currency_symbol ?><span class="course-widget-price-amount"></span>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($result['social_media'])): ?>
                <div class="course-widget-summary">
                    <?= View::factory('frontend/snippets/widget_social_media', array('social_media' => $result['social_media'])) ?></div>
            <?php endif; ?>

            <?php if (empty($result['tags']) && isset($result['summary']) && trim($result['summary'])): ?>
                <div class="course-widget-summary list_only"><?= $result['summary'] ?></div>
            <?php endif; ?>

            <div class="course-widget-links">
                <button type="submit" class="button button--view">
                    <?= !empty($result['button_text']) ? $result['button_text'] : __('View Details') ?>
                </button>

                <?php if ($account_bookings && $contact_id): ?>
                    <?php $in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $schedule['id']))) ?>

                    <button type="button" class="button button--book wishlist_add <?=$in_wishlist == 0 ? '' : 'hidden'?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule['id']?>"><?= __('Add To Wishlist') ?></button>

                    <button type="button" class="button button--cl_remove wishlist_remove <?=$in_wishlist == 0 ? 'hidden' : ''?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule['id']?>"><?= __('Remove From Wishlist') ?></button>
                <?php endif; ?>

                <?php if (!empty($result['link_2'])): ?>
                    <button type="submit" action="<?= $result['link_2'] ?>" class="button button--enquire">
                        <?= !empty($result['button_text_2']) ? $result['button_text_2'] : __('Enquire Now') ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<?php
// Normalise input
$family = !empty($family) ? $family : null;
$family = (is_object($family) && get_class($family) == 'Model_Family') ? $family : new Model_Family($family);
$active = !empty($active) ? $active : array();
$active = is_array($active) ? $active : array($active);
$attributes = (isset($attributes) && is_array($attributes)) ? $attributes : array();
$attributes['class'] = 'collapse collapse-xs-only family-member-selector btn-group btn-group-lg btn-group-pills'.(isset($attributes['class']) ? ' '.$attributes['class'] : '');
$attributes['id'] = isset($attributes['id']) ? $attributes['id'] : 'family-member-selector-'.$family->get_id();
$attributes['aria-expanded'] = 'false';

$members = $family->get_members();
?>

<button
    type="button"
    aria-expanded="false"
    class="btn btn-default btn-lg hidden-sm hidden-md hidden-lg hidden-xl collapsed"
    data-toggle="collapse"
    data-target="#<?= $attributes['id'] ?>"
    style="border-width: 1px 0; width: 100%;"
    >
    <?= __('Select family member') ?> <span class="arrow_caret-down"></span>
</button>

<div
    <?= HTML::attributes($attributes) ?>
    data-family_id="<?= $family->get_id() ?>"
    data-primary_contact_id="<?= $family->get_primary_contact_id() ?>"
    >

    <label class="checkbox-icon family-selector-member btn-group-pills-all family-selector-member--all">
        <input type="checkbox" checked="checked" />
        <span class="checkbox-icon-unchecked btn btn-success">
            <span><strong><?= __('Select $1 all', array('$1' => '<br class="hidden-xs" />')) ?></strong></span>
        </span>

        <span class="checkbox-icon-checked btn btn-success">
            <span><strong><?= __('Unselect $1 all', array('$1' => '<br class="hidden-xs" />')) ?></strong></span>
        </span>
    </label>

    <?php foreach ($members as $member): ?>
        <label class="checkbox-icon family-selector-member">
            <input
                type="checkbox"
                name="family_members[]"
                value="<?= $member->get_id() ?>"
                class="family-member-checkbox"
                data-contact_id="<?= $member->get_id() ?>"
                data-is_primary="<?= $member->get_is_primary() ?>"
                checked="checked"
                />

            <span class="checkbox-icon-unchecked btn btn-default">
                <span>
                    <?= trim($member->get_first_name().' '.$member->get_last_name()) ?>
                    <br class="hidden-xs" />
                    <strong><?= $contact_role ?></strong>
                </span>
            </span>

            <span class="checkbox-icon-checked btn btn-primary">
                <span>
                    <?= trim($member->get_first_name().' '.$member->get_last_name()) ?>
                    <br class="hidden-xs" />
                    <strong><?= $contact_role ?></strong>
                </span>
            </span>
        </label>
    <?php endforeach; ?>
</div>

<script>
    $(document).on('change', '.family-selector-member--all [type="checkbox"]', function()
    {
        var $selector          = $(this).parents('.family-member-selector');
        var $member_checkboxes = $selector.find('.family-member-checkbox');
        var all_selected       = ($selector.find('.family-member-checkbox:not(:checked)').length == 0);

        // If "all" is checked, check everything else, unless everything else is already checked. In which case, un-check everything.
        if (this.checked && !all_selected) {
            $member_checkboxes.prop('checked', true).trigger('change');
        } else {
            $(this).prop('checked', false);
            $member_checkboxes.prop('checked', false).trigger('change');
        }
    });

    $(document).on('change', '.family-member-checkbox', function(ev)
    {
        // Only continue if this was triggered by a human action, not the .trigger() method
        if (ev.originalEvent !== undefined) {
            var $selector       = $(this).parents('.family-member-selector');
            var $select_all     = $selector.find('.family-selector-member--all [type="checkbox"]');
            var $others_checked = $selector.find('.family-member-checkbox:checked').not(this);

            // When you click on a family member, de-select all other family members
            $others_checked.prop('checked', false).trigger('change');
            $select_all.prop('checked', false);

            // Ensure this stays selected when clicked on. Unless everything else was also unselected.
            if (!this.checked && $others_checked.length) {
                ev.preventDefault();
                $(this).prop('checked', true).trigger('change');
                return false;
            }
        }
    });

    function abc(){}
</script>
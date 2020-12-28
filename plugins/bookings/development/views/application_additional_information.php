<div>
    <h2 class="checkout-heading contact-details-heading">
        <span class="fa fa-info-circle"></span>
        <?= __('Additional information') ?>
    </h2>

    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <div class="form-group">
                <div class="col-sm-6">
                    <label for="checkout-additional_information-heard_about_via"><?= __('How did you hear about us') ?></label>
                    <?php
                    $options = [
                        __('Advertisement'),
                        __('Booklet'),
                        __('Email/newsletter'),
                        __('Facebook'),
                        __('Family or friend'),
                        __('Newspaper'),
                        __('Search engine'),
                        __('Twitter'),
                        __('Other')
                    ];

                    $options = ['' => __('Please select')] + array_combine($options, $options);
                    $attributes = [
                        'id' => 'checkout-additional_information-heard_about_via',
                        'data-form-show-option' => __('Other'),
                        'data-target' => '#checkout-additional_information-heard_about_via_other-wrapper'
                    ];

                    echo Form::ib_select(null, 'heard_about_via', $options, @$extra_data['heard_about_via'], $attributes)
                    ?>
                </div>

                <div class="col-sm-6 hidden" id="checkout-additional_information-heard_about_via_other-wrapper">
                    <label for="checkout-additional_information-heard_about_via_other"><?= __('Please specify') ?></label>

                    <?php
                    $attributes = ['id' => 'checkout-additional_information-heard_about_via_other'];
                    echo Form::ib_input(null, 'heard_about_via_other', @$extra_data['heard_about_via_other'], $attributes);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

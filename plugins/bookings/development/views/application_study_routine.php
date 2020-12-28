<style>
    .form-group.no-gutters {
        margin-left: 0;
        margin-right: 0;
    }

    .checkout-average_study {
        display: flex;
        flex-wrap: wrap;
        text-align: center;
        margin-bottom: 1em;
    }

    .checkout-average_study > label {
        display: inline-block;
        flex: 1;
    }

    .checkout-average_study .form-radio-helper {
        margin: 0;
    }

    @media screen and (max-width: 767px) {
        .checkout-average_study {
            margin-bottom: 1em;
        }
    }
</style>

<div class="theme-form">
    <h3 class="checkout-heading contact-details-heading">
        <span class="fa fa-book"></span>
        <?= __('Study Routine') ?>
    </h3>

    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <div class="form-group no-gutters">
                <p class="col-sm-12"><?=__('Average study per week (in hours)')?></p>

                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="checkout-average_study col-xs-6 col-sm-3 col-md-3">
                        <?php for ($j = 1 ; $j <= 5 ; $j++): ?>
                            <label>
                                <?php $checked = @$application['data']['study_per_week'] == (4 * $i + $j); ?>
                                <?= 5 * $i + $j ?><br />
                                <?= Form::ib_radio(null, 'application[study_per_week]', 4 * $i + $j, $checked) ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="form-group">
                <p class="col-sm-12"><?=__('Study Routine')?></p>

                <div class="col-sm-12">
                    <?php
                    $options = array('Mon' => 'Mon', 'Tue' => 'Tue', 'Wed' => 'Wed', 'Thu' => 'Thu', 'Fri' => 'Fri', 'Sat' => 'Sat', 'Sun' => 'Sun');
                    echo Form::btn_options('application[study_routine][]', $options, @$application['data']['study_routine'], true, array(), array('class' => 'stay_inline'));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <p class="col-sm-12"><?=__('Rate your work ethic')?></p>

                <div class="col-sm-12">
                    <?php
                    $options = array();
                    for ($i = 1; $i <= 10; $i++) $options[$i] = $i;
                    echo Form::btn_options('application[work_ethic]', $options, @$application['data']['work_ethic'], false, array(), array('class' => 'stay_inline'));
                    ?>
                </div>

                <div class="col-xs-6 text-left"><?= __('Could be better') ?></div>

                <div class="col-xs-6 text-right"><?= __('Excellent') ?></div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="checkout-routine-struggle"><?= __('What areas do you struggle with?') ?></label>

                    <?= Form::ib_textarea(null, 'application[struggles]', @$application['data']['struggles'], array('id' => 'checkout-routine-struggle', 'rows' => 4)) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <h2 class="checkout-heading contact-details-heading">
        <span class="fa fa-plane"></span>
        <?= __('Travel details') ?>
    </h2>

    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <div class="alert alert-info">
                <div style="display: flex;">
                    <span style="padding-right: 1em;">
                        <span class="fa fa-info-circle" style="font-size: 2em;"></span>
                    </span>

                    <span>
                        <strong><?= __('If available, please let us know your travel plans.') ?></strong><br />
                        <?= __('We will use these details to meet you at the airport to ensure you get here safely.') ?>
                    </span>
                </div>
            </div>

            <?php
            $airport_options = [
                __('Belfast City Airport (BHD)'),
                __('Belfast International Airport (BFS)'),
                __('Cork Airport (ORK)'),
                __('Derry Airport (LDY)'),
                __('Donegal Airport (CFN)'),
                __('Dublin Airport (DUB)'),
                __('Ireland West Airport Knock (NOC)'),
                __('Kerry Airport (KIR)'),
                __('Shannon Airport (SNN)'),
                __('Waterford Airport (WAT)')
            ];
            $airport_options = ['' => __('Select')] + array_combine($airport_options, $airport_options);

            $flights = array('arrival' => __('Arrival'), 'departure' => __('Departure'));
            ?>

            <?php foreach ($flights as $flight_prefix => $flight): ?>
                <h3><?= $flight ?></h3>

                <div class="form-group vertically_center">
                    <div class="col-sm-3">
                        <label for="checkout-<?= $flight_prefix ?>_flight_number"><?= __('Flight number') ?></label>
                        <?= Form::ib_input(null, $flight_prefix.'_flight_number', @$extra_data[$flight_prefix.'_flight_number'], ['id' => 'checkout-'.$flight_prefix.'_flight_number']); ?>
                    </div>

                    <div class="col-sm-3">
                        <label for="checkout-<?= $flight_prefix ?>_flight_date"><?= __('Date') ?></label>

                        <?php
                        $attributes = ['class' => 'datepicker', 'id' => 'checkout-'.$flight_prefix.'_flight_date'];
                        $args = ['right_icon' => '<span class="fa fa-calendar"></span>'];
                        echo Form::ib_input(null, $flight_prefix.'_flight_date', @$extra_data[$flight_prefix.'_flight_date'], $attributes, $args);
                        ?>
                    </div>

                    <div class="col-sm-3">
                        <label for="checkout-<?= $flight_prefix ?>_flight_time"><?= __('Time') ?></label>

                        <?php
                        $attributes = ['class' => 'timepicker', 'id' => 'checkout-'.$flight_prefix.'_flight_time', 'data-step' => 5];
                        $args = ['right_icon' => '<span class="fa fa-clock-o"></span>'];
                        echo Form::ib_input(null, $flight_prefix.'_flight_time', @$extra_data[$flight_prefix.'_flight_time'], $attributes, $args);
                        ?>
                    </div>

                    <div class="col-sm-3">
                        <label for="checkout-<?= $flight_prefix ?>_airport"><?= __('Airport') ?></label>

                        <?php
                        $attributes = ['id' => 'checkout-'.$flight_prefix.'_airport'];
                        echo Form::ib_select(null, $flight_prefix.'_airport', $airport_options, @$extra_data[$flight_prefix.'_airport'], $attributes)
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (in_array($custom_checkout, ['bc_language', 'sls'])): ?>
                <h3><?= __('Airport transfer') ?></h3>
            <?php endif; ?>

            <?php if ($custom_checkout == 'bc_language'): ?>
                <p><?= __('All junior students are required to book an airport transfer with $1 for arrival and departure.', ['$1' => Settings::instance()->get('company_name')]) ?></p>

                <div class="form-group">
                    <div class="col-sm-12">
                        <?= Form::ib_checkbox(__('Transfers from Cork and Kerry airport = $1', ['$1' => __('no charge')]), 'transfer_cork_kerry', 1, @$extra_data['transfer_cork_kerry'] == 1) ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <?= Form::ib_checkbox(__('Transfers from Dublin - one way = $1', ['$1' => '&euro;100']), 'transfer_dublin_one_way', 1, @$extra_data['transfer_dublin_one_way'] == 1) ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <?= Form::ib_checkbox(__('Transfers from Dublin - return = $1', ['$1' => '&euro;200']), 'transfer_dublin_return', 1, @$extra_data['transfer_dublin_return'] == 1) ?>
                    </div>
                </div>
            <?php elseif ($custom_checkout == 'sls'): ?>
                <?php
                $airport_transfer_options = [
                    '' => '',
                    'arrival_and_departure' => 'Arrival and departure',
                    'arrival'               => 'Arrival only',
                    'departure'             => 'Departure only',
                    'none'                  => 'None'
                ];

                echo Form::ib_select('Airport transfer required', 'airport_transfer', $airport_transfer_options, @$extra_data['airport_transfer'], ['class' => 'validate[required]']);
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>
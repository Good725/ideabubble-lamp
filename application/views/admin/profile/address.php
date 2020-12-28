<div class="edit_profile_wrapper" id="edit_profile_wrapper">
<form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=address" method="post">

    <?= $section_switcher ?>

    <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />

    <?php foreach($notifications as $notification) { ?>
        <input name="contactdetail_id[]" type="hidden" value="<?= $notification['id'] ?>" />
        <input name="contactdetail_type_id[]" type="hidden" value="<?= $notification['type_id'] ?>" />
        <input name="contactdetail_value[]" type="hidden" value="<?= $notification['value'] ?>" />
    <?php } ?>

    <section>
    <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) { ?>
    <div>
        <div id="header_buttons"></div>
        <div class="expand-section-tabs">
            <ul class="nav nav-tabs nav-tabs-contact">
                <li class="active"><a href="#personal-address-tab" data-toggle="tab"><?=__('Personal')?></a></li>

                <?php if (Settings::instance()->get('contacts_create_family')): ?>
                    <li><a href="#family-address-tab" data-toggle="tab"><?=__('Family')?></a></li>
                <?php endif; ?>

                <li><a href="#billing-address-tab" data-toggle="tab"><?=__('Billing')?></a></li>
            </ul>
        </div>
    </div>
    <?php } ?>
    <div class="tab-content">
        <div class="tab-pane active" id="personal-address-tab">
            <input type="hidden" name="address[personal][address_id]" value="<?= (Model_Plugin::is_enabled_for_role('Administrator',
                'contacts3')) ? $contact3->get_address()->get_address_id() : ''; ?>"/>
            <?php
            $addressm = new Model_Residence();
            ?>
            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 1'), 'address[personal][address1]', Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_address1() : $contact->get_address1()); ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 2'), 'address[personal][address2]', Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_address2() : $contact->get_address2()); ?>
                </div>
            </div>
            <?php // Don't show address line 3 for contacts2 as address3 is county input
                if(Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')): ?>
                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Address line 3'), 'address[personal][address3]', Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_address3() : $contact->get_address3()); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('City'), 'address[personal][town]', Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_town() : $contact->get_address4()); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($counties as $county) {
                        $options[$county['id']] = $county['name'];
                    }
                    echo Form::ib_select(__('County'), 'address[personal][county]', $options, Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_county() : Model_Residence::get_county_id($contact->get_address3()));
                    ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Postcode'), 'address[personal][postcode]', Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_postcode() : $contact->get_postcode()); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($addressm->get_all_countries() as $country) {
                        $options[$country['code']] = $country['name'];
                    }
                    $selected = Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->address->get_country() : Model_Country::get_country($contact->get_country_id())['code'] ?? '0';
                    echo Form::ib_select(__('Country'), 'address[personal][country]', $options, $selected, array('class' => 'ib-combobox'));
                    ?>
                </div>
            </div>
        </div>

        <?php if (Settings::instance()->get('contacts_create_family')): ?>
            <div class="tab-pane" id="family-address-tab">
                <input type="hidden" name="address[family][address_id]" value="<?= (Model_Plugin::is_enabled_for_role('Administrator',
                    'contacts3')) ? $contact3->get_family()->get_residence() : ''; ?>"/>
                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Address line 1'), 'address[family][address1]'); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Address line 2'), 'address[family][address2]'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Address line 3'), 'address[family][address3]'); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('City'), 'address[family][town]'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '');
                        foreach ($counties as $county) {
                            $options[$county['id']] = $county['name'];
                        }
                        echo Form::ib_select(__('County'), 'address[family][county]', $options);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Postcode'), 'address[family][postcode]'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '');
                        foreach ($addressm->get_all_countries() as $country) {
                            $options[$country['code']] = $country['name'];
                        }
                        echo Form::ib_select(__('Country'), 'address[billing][country]', $options, null, array('class' => 'ib-combobox'));
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="tab-pane" id="billing-address-tab">
            <input type="hidden" name="address[billing][address_id]" value="<?= (Model_Plugin::is_enabled_for_role('Administrator',
                'contacts3')) ? $contact3->get_billing_address()->get_address_id() : ''; ?>"/>
            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(
                        __('Address line 1'),
                        'address[billing][address1]',
                        Model_Plugin::is_enabled_for_role('Administrator','contacts3')
                            ? $contact3->get_billing_address()->get_address1() : '',
                        ['readonly' => $billing_address_readonly]
                    ); ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 2'),
                        'address[billing][address2]',
                        Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
                            ? $contact3->get_billing_address()->get_address2() : '',
                        ['readonly' => $billing_address_readonly]
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?= Form::ib_input(__('Address line 3'),
                        'address[billing][address3]',
                        Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
                            ? $contact3->get_billing_address()->get_address3() : '',
                        ['readonly' => $billing_address_readonly]
                    ); ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(__('City'),
                        'address[billing][town]',
                        Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
                            ? $contact3->get_billing_address()->get_town() : '',
                        ['readonly' => $billing_address_readonly]
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($counties as $county) {
                        $options[$county['id']] = $county['name'];
                    }

                    $value = Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
                        ? $contact3->get_billing_address()->get_county()
                        : '';

                    echo Form::hidden('address[billing][county]', $value);

                    echo Form::ib_select(
                        __('County'),
                        'address[billing][county]',
                        $options,
                        $value,
                        ['disabled' => $billing_address_readonly]
                    );
                    ?>
                </div>

                <div class="col-sm-6">
                    <?= Form::ib_input(
                        __('Postcode'),
                        'address[billing][postcode]',
                        Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
                            ? $contact3->get_billing_address()->get_postcode() : '',
                        ['readonly' => $billing_address_readonly]
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <?php
                    $options = array('' => '');
                    foreach ($addressm->get_all_countries() as $country) {
                        $options[$country['code']] = $country['name'];
                    }
                    $selected = Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? $contact3->get_billing_address()->get_country() : '';
                    $args = [
                        'class' => $billing_address_readonly ? null : 'ib-combobox',
                        'disabled' => $billing_address_readonly
                    ];
                    echo Form::hidden('address[billing][county]', $selected);
                    echo Form::ib_select(__('Country'), 'address[billing][country]', $options, $selected, $args);
                    ?>
                </div>
            </div>
        </div>
    </div>
    </section>

    <section>
        <div class="form-action-group" id="ActionMenu">
            <button type="submit" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
            <button type="submit" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
            <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
            <a href="/admin" class="btn btn-cancel">Cancel</a>
        </div>
    </section>
</form>
</div>
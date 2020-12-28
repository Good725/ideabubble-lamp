<?php echo IbHelpers::get_messages(); ?>

<form id="pl_member_registration_form" action="" method="POST">
    <h2>Rewards Club - Registration Area</h2>

    <div id="pl_member_registration_card_number_msg_area" class="note">
        <div class="title">Please Note:</div>
        <div id="note_1">* If you already have a "Rewards Club" card and you wish to register it in our "Rewards Club"
            online system, please <a href="javascript:addCardNumber('has_card');">click here</a>.
        </div>
        <div id="note_2">
            <p>* Registration of Existent Cards, might take up to 48 hours in order to get the card activated.</p>

            <p>* Please ensure EITHER your <b>First Name</b>, <b>Last Name</b> and <b>Email</b> OR your <b>First
                    Name</b>, <b>Last Name</b> and <b>Date of Birth</b> match your existing offline cards details.</b>
            </p>

            <p>* Once the card is activated for online use, you will get an email confirmation with your "Rewards Club"
                online login details.</p>
        </div>
    </div>
    <fieldset class="">
        <input type="hidden" value="43" name="pl_language_id" id="pl_member_registration_language_id">
        <input type="hidden" value="0" name="pl_contact_for_research">
        <input type="hidden" value="0" name="pl_contact_by_partners">

        <div id="registration_card_no">
            <label for="pl_member_registration_f_name" class="left clear_left">Card No</label>
            <input type="text" placeholder="Card Number" class="left validate[required,length[0,20]] text-input"
                   id="pl_card_number" name="pl_card_number" value="0">
            <span class="left red_text required">*</span>
        </div>

        <label for="pl_member_registration_gender_id" class="left clear_left">Gender</label>
        <select name="pl_gender_id" id="pl_member_registration_gender_id" class="left validate[required]">
            <option value="">-- Gender --</option>
            <option value="1">Male</option>
            <option value="2">Female</option>
        </select>
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_title_id" class="left clear_left">Title</label>
        <select name="pl_title_id" id="pl_member_registration_title_id" class="left validate[required]">
            <option value="">-- Title --</option>
            <option value="1">Mr</option>
            <option value="2">Mrs</option>
            <option value="3">Miss</option>
            <option value="4">Ms</option>
            <option value="5">Dr</option>
            <option value="6">Fr</option>
            <option value="7">Prof</option>
        </select>
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_f_name" class="left clear_left">First Name</label>
        <input type="text" placeholder="First Name" class="left validate[required,length[0,20]] text-input"
               id="pl_member_registration_f_name" name="pl_f_name">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_s_name" class="left clear_left">Surname</label>
        <input type="text" placeholder="Surname" class="left validate[required,length[0,20]] text-input"
               id="pl_member_registration_s_name" name="pl_s_name">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_dob" class="left clear_left">Date of Birth</label>
        <input type="text" placeholder="Date of Birth" class="left validate[required,length[0,10]] text-input"
               id="pl_member_registration_dob" name="pl_dob">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_addr1" class="left clear_left">Address line 1</label>
        <input type="text" placeholder="Address Line 1" class="left validate[required,length[0,50]] text-input"
               id="pl_member_registration_addr1" name="pl_addr1">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_addr2" class="left clear_left">Address line 2</label>
        <input type="text" placeholder="Address Line 2" class="left validate[required,length[0,50]] text-input"
               id="pl_member_registration_addr2" name="pl_addr2">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_addr3" class="left clear_left">Address line 3</label>
        <input type="text" placeholder="Address Line 3" class="left" id="pl_member_registration_addr3" name="pl_addr3">

        <label for="pl_member_registration_addr4" class="left clear_left">Address line 4</label>
        <input type="text" placeholder="Address line 4" class="left" id="pl_member_registration_addr4" name="pl_addr4">

        <label for="pl_member_registration_country_id" class="left clear_left">Country</label>
        <select name="pl_country_id" id="pl_member_registration_country_id" class="left validate[required]">
            <option value="0">-- Country --</option>
            <option value="105">Ireland</option>
        </select>

        <label for="pl_member_registration_email" class="left clear_left">Email</label>
        <input type="text" placeholder="Email" class="left validate[required,custom[email]] text-input"
               id="pl_member_registration_email" name="pl_email">
        <span class="left red_text required">*</span>

        <label for="pl_member_registration_mobile" class="left clear_left">Mobile</label>
        <input type="text" placeholder="Mobile" class="left" id="pl_member_registration_mobile" name="pl_mobile">


        <input type="hidden" value="0" name="pl_member_registration_contact_for_research"
               id="pl_member_registration_contact_for_research">
        <input type="hidden" value="0" name="pl_member_registration_contact_by_partners"
               id="pl_member_registration_contact_by_partners">
    </fieldset>

    <div id="contact_confirmations" class="left">
        <p class="note">
            We want to ensure that you are aware of all our offers that will be made available to "Rewards Club""
            members. To do this we need your agreement to communication with you via email and text. Nobody wants to be
            annoyed by irrelevant texts or emails and we promise not to abuse your trust. You can also opt out at any
            time. We are also committed to protecting the environment by eliminating the use of paper wherever possible
            so will not be sending out letters. If we cannot communicate with you then we cannot tell you about specific
            offers available. If you <span class="orange_text"><strong>do not</strong></span> wish to be contacted with
            offers or rewards please tick the appropriate box:
            <input id="pl_member_registration_contact_for_info" name="pl_contact_for_info" class="checkbox_input"
                   type="checkbox" value="1">
            <label for="pl_member_registration_contact_for_info" class="inner_label">Email</label>
            <input id="pl_member_registration_contact_by_sms" name="pl_contact_by_sms" class="checkbox_input"
                   type="checkbox" value="1">
            <label for="pl_member_registration_contact_by_sms" class="inner_label">Text</label>.
        </p>
    </div>

    <button type="button" name="pl_member_registration_button" id="pl_member_registration_button" class="left pl_button"
            onclick="validatePlRegistrationForm();">
        Register
    </button>

</form>

<link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/jqueryui-lightness/jquery-ui-1.10.3.min.css" rel="stylesheet"
      type="text/css"/>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript">
    function validatePlRegistrationForm() {
        var valid = $('#pl_member_registration_form').validationEngine('validate');
        setTimeout('removeBubbles()', 5000);
        if (valid) {
            $('#pl_member_registration_form').attr('action', '/frontend/paybackloyalty/register_new_member');
            $('#pl_member_registration_form').submit();
        }
    }
    function removeBubbles() {
        $('.formError').each(function (i, e) {
            document.body.removeChild(e);
        });
    }

    function addCardNumber(member) {
        if (member == 'has_card') {
            $('#pl_card_number').val('');
        }
        else {
            $('#pl_card_number').val(0);
        }

        $("#note_1").toggle();
        $("#note_2").toggle();
        $("#registration_card_no").toggle();
    }

    $(function () {
        $("#pl_member_registration_dob").datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: "-100y",
            maxDate: "-18y",
            dateFormat: 'dd-mm-yy',
            yearRange: '1930:2012'
        });
    });
</script>
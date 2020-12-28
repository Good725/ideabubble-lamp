<?php
echo IbHelpers::get_messages();

$session = Session::instance();


if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])): ?>

<?php
    //Data to be displayed
    //Current card
    $cards_options = '';
    foreach($_SESSION['pl_user']['account_cards'] as $key => $value){
        if($value['devicestatus'] == 'DEVICE ACTIVE'){
            if($value['devicereference'] == $_SESSION['pl_user']['account_card_in_use']){
                $cards_options_seleted = 'selected="selected"';
            }
            else{
                $cards_options_seleted = '';
            }
            $cards_options .= '<option value="'. $value['devicereference'] .'" '. $cards_options_seleted .' >'. $value['devicereference'] .'</option>';
        }
    }

    //birthday
    $birthday = date('d-m-Y', strtotime($_SESSION['pl_user']['dateofbirth']));

    //Gender
    if($_SESSION['pl_user']['persongenderid'] == '1'){
        $gender_male_selected   = 'selected="selected"';
        $gender_female_selected = '';
    }
    else{
        $gender_male_selected   = '';
        $gender_female_selected = 'selected="selected"';
    }

    //Title
    $title_option_mr   = '';
    $title_option_mrs  = '';
    $title_option_miss = '';
    $title_option_ms   = '';
    $title_option_dr   = '';
    $title_option_fr   = '';
    $title_option_prof = '';

    switch($_SESSION['pl_user']['persongenderid']){
        case '1':
            $title_option_mr   = 'selected="selected"';
        case '2':
            $title_option_mrs  = 'selected="selected"';
        case '3':
            $title_option_miss = 'selected="selected"';
        case '4':
            $title_option_ms   = 'selected="selected"';
        case '5':
            $title_option_dr   = 'selected="selected"';
        case '6':
            $title_option_fr   = 'selected="selected"';
        case '7':
            $title_option_prof = 'selected="selected"';
    }

    //Contact for research
    if($_SESSION['pl_user']['contactforresearch'] == '1'){
        $contact_research_yes_opt = 'selected="selected"';
        $contact_research_no_opt  = '';
    }
    else{
        $contact_research_yes_opt = '';
        $contact_research_no_opt  = 'selected="selected"';
    }

    //Contact by partners
    if($_SESSION['pl_user']['contactbypartners'] == '1'){
        $contact_partners_yes_opt = 'selected="selected"';
        $contact_partners_no_opt  = '';
    }
    else{
        $contact_partners_yes_opt = '';
        $contact_partners_no_opt  = 'selected="selected"';
    }

    //Contacts by sms
    if($_SESSION['pl_user']['contactbysms'] == '1'){
        $contact_sms_yes_opt = 'selected="selected"';
        $contact_sms_no_opt  = '';
    }
    else{
        $contact_sms_yes_opt = '';
        $contact_sms_no_opt  = 'selected="selected"';
    }
?>

<div id="pb_members_area">
    <div id="tabs">
        <ul id="tabs_ul">
            <li><a href="#tabs-1" id="tabs-1-btn">Account</a></li>
            <li><a href="#tabs-2" id="tabs-2-btn">Transactions</a></li>
            <li><a href="#tabs-3" id="tabs-3-btn">Points</a></li>
            <li><a href="#tabs-4" id="tabs-4-btn">Password editor</a></li>
        </ul>
        <a href="/frontend/paybackloyalty/user_logout"><div type="button" id="pl_member_area_logout_button" >Logout</div></a>
        <div id="tabs-1">
            <form action="/frontend/paybackloyalty/save_account_details" method="post" enctype="application/x-www-form-urlencoded" id="frm_account">
                <input type="hidden" id="pl_language_id" name="pl_language_id" value="43">
                <input type="hidden" id="pl_contact_for_info" name="pl_contact_for_info" value="no" />
            <fieldset>
                <legend>Account Details</legend>
                <div>
                    <div class="text_label">Username</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['username']?>" readonly="readonly">
                    </div>
                </div>
                <div>
                    <div class="text_label">Current Card</div>
                    <div class="text_input">
                        <div class="text_input">
                            <input type="text" value="<?=@$_SESSION['pl_user']['account_card_in_use']?>" readonly="readonly">
                        </div>
                    </div>
                </div>
                <?php if(count($_SESSION['pl_user']['account_cards']) > 1): ?>
                <div>
                    <div class="text_label">Card List</div>
                    <div class="text_input">
                        <select id="card_in_use" multiple="multiple" readonly="readonly">
                            <?=@$cards_options?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
            </fieldset>

            <fieldset>
                <legend>Personal Details</legend>
                <div>
                    <div class="text_label">Gender</div>
                    <div class="text_input">
                        <select name="pl_gender_id" class="validate[required]" id="pl_gender_id">
                            <option value="">-- Gender --</option>
                            <option value="1" <?=@$gender_male_selected?> >Male</option>
                            <option value="2" <?=@$gender_female_selected?> >Female</option>
                        </select>
                        <span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Title</div>
                    <div class="text_input">
                        <select name="pl_title_id" id="title" class="pl_title_id">
                            <option value="0">-- Title --</option>
                            <option value="1" <?=$title_option_mr?>>Mr</option>
                            <option value="2" <?=$title_option_mrs?>>Mrs</option>
                            <option value="3" <?=$title_option_miss?>>Miss</option>
                            <option value="4" <?=$title_option_ms?>>Ms</option>
                            <option value="5" <?=$title_option_dr?>>Dr</option>
                            <option value="6" <?=$title_option_fr?>>Fr</option>
                            <option value="7" <?=$title_option_prof?>>Prof</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="text_label">First Name</div>
                    <div class="text_input" >
                        <input type="text" value="<?=@$_SESSION['pl_user']['forename']?>" class="validate[required,minSize[1],maxSize[40]]" id="pl_f_name" name="pl_f_name"><span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Surname</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['surname']?>" name="pl_s_name" id="pl_s_name" class="validate[required,minSize[1],maxSize[40]]"><span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Date of Birth</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$birthday?>" class="datepicker dob validate[required,minSize[1],maxSize[10]]" name="pl_dob" id="pl_dob"><span class="req_symb">*</span>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Contact Details</legend>
                <div>
                    <div class="text_label">Address Line 1</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['addressline1']?>" name="pl_addr1" id="pl_addr1" class="validate[required,minSize[1],maxSize[50]]"><span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Address Line 2</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['addressline2']?>" name="pl_addr2" id="pl_addr2" class="validate[required,minSize[1],maxSize[50]]"><span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Address Line 3</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['addressline3']?>" name="pl_addr3">
                    </div>
                </div>
                <div>
                    <div class="text_label">Address Line 4</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['addressline4']?>" name="pl_addr4">
                    </div>
                </div>
                <div>
                    <div class="text_label">Country</div>
                    <div class="text_input">
                        <select name="pl_country_id" id="pl_country_id" class="validate[required]">
                            <option value="0">-- Country --</option>
                            <option value="105" selected="selected">Ireland</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="text_label">Email</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['emailaddress']?>" name="pl_email" id="pl_email" class="validate[required,custom[email]]"><span class="req_symb">*</span>
                    </div>
                </div>
                <div>
                    <div class="text_label">Mobile</div>
                    <div class="text_input">
                        <input type="text" value="<?=@$_SESSION['pl_user']['mobilephone']?>" name="pl_mobile" >
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Account Settings</legend>
                <div>
                    <div class="text_label_floating">Contact for Research</div>
                    <div class="text_input_floating">
                        <select name="pl_contact_for_research">
                            <option value="1" <?=@$contact_research_yes_opt?>>Yes</option>
                            <option value="0" <?=@$contact_research_yes_opt?>>No</option>
                        </select>
                    </div>
                    <div class="text_label_floating">Contact by Partners</div>
                    <div class="text_input_floating">
                        <select name="pl_contact_by_partners">
                            <option value="1" <?=@$contact_partners_yes_opt?>>Yes</option>
                            <option value="0" <?=@$contact_partners_yes_opt?>>No</option>
                        </select>
                    </div>
                    <div class="text_label_floating">Contact by SMS</div>
                    <div class="text_input_floating">
                        <select name="pl_contact_by_sms">
                            <option value="1" <?=@$contact_sms_yes_opt?>>Yes</option>
                            <option value="0" <?=@$contact_sms_yes_opt?>>No</option>
                        </select>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <button id="pl_save_account_changes" type="button" onclick="validatePlAccountChanges();">Save Changes</button>
            </fieldset>
            </form>
        </div>
        <div id="tabs-2">
            <form action="" method="post" enctype="application/x-www-form-urlencoded" id="pl_trans_hist_form">
                <fieldset>
                    <legend>Transactions Period</legend>
                    <div>
                        <div class="text_label">From Date</div>
                        <div class="text_input">
                            <input type="text" name="pl_from_date" id="pl_from_date" class="validate[required]"><span class="req_symb">*</span>
                        </div>
                    </div>
                    <div>
                        <div class="text_label">To Date</div>
                        <div class="text_input">
                            <input type="text" name="pl_to_date" id="pl_to_date" class="validate[required]"><span class="req_symb">*</span>
                        </div>
                    </div>
                    <div>
                        <div class="text_label">No of Records</div>
                        <div class="text_input">
                            <input type="text" name="pl_max_records" class="validate[required,custom[integer]]" id="number_of_records"><span class="req_symb">*</span>
                        </div>
                    </div>
                    <button id="pl_get_transactions_info" type="button" onclick="validateTransHistPeriod();">Get Transactions Info »</button>
                </fieldset>
                <fieldset>
                    <legend>Rewards Club Transactions</legend>
                    <table id="pl_transaction_history_table">
                        <thead>
                            <tr><th>Date</th><th>Value €</th><th>Points</th><th>Spend</th></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </fieldset>
            </form>
        </div>
        <div id="tabs-3">
            <fieldset>
                <legend>Remaining Points</legend>
                <div>
                    <div class="text_label">Rewards Club Card No</div>
                    <div class="text_input">
                        <select name="card_in_use" id="card_in_use_points_tab" onchange="update_account_card_in_use(this)">
                            <option value="">-- Please Select --</option>
                            <?=@$cards_options?>
                        </select>
                    </div>
                </div>
                <div class="remaining_points_label">Points on card</div>    <div class="remaining_points_value" id="points_on_card"><?=@(int)$_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance']?></div>
                <div class="remaining_points_label">Points on account</div> <div class="remaining_points_value"><?=@(int)$_SESSION['pl_user']['loyaltybalance']?></div>
            </fieldset>
        </div>
        <div id="tabs-4">
            <fieldset>
                <form action="/frontend/paybackloyalty/update_password" method="post" enctype="application/x-www-form-urlencoded" id="frm_password">
                    <legend>Update Your Password</legend>
                    <div>
                        <div class="text_label">Current Password</div>
                        <div class="text_input">
                            <input type="password" name="pl_oldpassword" id="pl_oldpassword" class="validate[required,maxSize[20]]" ><span class="req_symb">*</span>
                        </div>
                    </div>
                    <div>
                        <div class="text_label">New Password</div>
                        <div class="text_input">
                            <input type="password" name="pl_newpassword" id="pl_newpassword" class="validate[required,maxSize[20]]" ><span class="req_symb">*</span>
                        </div>
                    </div>
                    <div>
                        <div class="text_label">Confirm Password</div>
                        <div class="text_input">
                            <input type="password" name="pl_confirmpassword" id="pl_confirmpassword" class="validate[required,maxSize[20]]" ><span class="req_symb">*</span>
                        </div>
                    </div>
                    <button type="button" id="pl_get_transactions_info" onclick="validatePlPasswordChanges()">Save Password</button>
                </form>
            </fieldset>
        </div>
    </div>
</div>
<link href="<?=URL::get_engine_plugin_assets_base('products')?>css/front_end/jqueryui-lightness/jquery-ui-1.10.3.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/front_end/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript">
    $(function() {
        //initializes TABS
        $( "#tabs" ).tabs();
        //initializes Datapickers
        $( "#pl_from_date, #pl_to_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
        $( "#pl_dob, #pl_to_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: "-100y",
            maxDate: "-18y",
            dateFormat: 'dd-mm-yy'
        });
    });

    function validatePlAccountChanges(){
        var valid = $('#frm_account').validationEngine('validate');
        setTimeout('removeBubbles()', 5000);
        if (valid){
            $('#frm_account').submit();
        }
    }

    function validatePlPasswordChanges(){
        var valid = $('#frm_password').validationEngine('validate');
        setTimeout('removeBubbles()', 5000);
        if (valid){
            $('#frm_password').submit();
        }
    }

    function removeBubbles() {
        $('.formError').each(function(i,e){document.body.removeChild(e);});
    }

    function validateTransHistPeriod(){
        var valid = false;
        valid = $('#pl_trans_hist_form').validationEngine('validate');
        //Check Dates Period
        if(valid){
            var from_date = new Date(
                //Set the Date Year
                $('#pl_from_date').val().split('-')[2],
                //Set the Date Month (0-11 for Jan - Dec)
                ($('#pl_from_date').val().split('-')[1]-1),
                //Set the Date Day
                $('#pl_from_date').val().split('-')[0]);
            var to_date = new Date(
                $('#pl_to_date').val().split('-')[2],
                ($('#pl_to_date').val().split('-')[1]-1),
                $('#pl_to_date').val().split('-')[0]
            );
            if(from_date > to_date){
                valid = false;
                alert('"Date To: '+$('#pl_to_date').val()+'" cannot be before "Date From: '+$('#pl_from_date').val()+'"');
            }
        }
        //Submit Form
        if (valid){
            var data = $('#pl_trans_hist_form').serialize();
            $('#pl_trans_hist_form tbody tr').remove();
            $.post('/frontend/paybackloyalty/get_transaction_info', data, function(transactions, status){
                if(status == 'success'){
                    var length = transactions.length;
                    for(var i = 0; i < length; i++){
                        $('#pl_trans_hist_form tbody').append('<tr><td>'+ transactions[i].transdate +'</td><td>'+ transactions[i].totalvalue +'</td><td>'+ transactions[i].points +'</td><td>'+ transactions[i].spend +'</td></tr>');
                    }
                }
            }, 'json')
        }else{
            setTimeout('removeBubbles()', 5000);
        }
    }//end of function


    update_account_card_in_use = function(element){
        var data = { card_num_to_use: element.value };

        if(element.value == '0'){
            display_message('error', 'Please select a card');
        }
        else{
            $.post('/frontend/paybackloyalty/get_car_info', data, function(card, status){

                //Update Rewards Club points displayed
                number = parseFloat(card.card_loyalty_points * 100);
                number = parseInt(number.toFixed(1));
                $('#points_on_card').html(number);

            }, 'json');
        }
    }
</script>

<?php endif;?>
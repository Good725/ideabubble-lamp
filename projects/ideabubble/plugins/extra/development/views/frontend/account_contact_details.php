<div class="confirmation_message"></div>
<h2>Your Contact Details</h2>
<form class="service-form" action="frontend/extra/save_customer" method="post" id="contact_payment_form">
    <input type="hidden" name="checkout_data" id="checkout_data"/>
    <input type="hidden" name="id" value="<?= $customer['id'] ?>" />
    <input type="hidden" name="contact" value="<?= $customer['contact'] ?>" />
    <input type="hidden" name="billing_contact" value="<?= $customer['billing_contact'] ?>" />

    <fieldset class="form-block">
        <legend>Company Details</legend>

        <div>
            <label class="form-label" for="contact_details_company_title">Company</label>
            <input id="contact_details_company_title" type="text" name="company_title" value="<?= $customer['company_title'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_address1">Address 1</label>
            <input id="contact_details_address1" type="text" name="address1" value="<?= $customer['address1'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_address2">Address 2</label>
            <input id="contact_details_address2" type="text" name="address2" value="<?= $customer['address2'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_address3">Address 3</label>
            <input id="contact_details_address3" type="text" name="address3" value="<?= $customer['address3'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_county">County</label>
            <select id="contact_details_county" name="county">
                <?= $counties ?>
            </select>
        </div>

        <div>
            <label class="form-label" for="contact_details_phone">Phone</label>
            <input id="contact_details_phone" type="text" name="phone" value="<?= $customer['phone'] ?>" />
        </div>
    </fieldset>

    <fieldset class="form-block">
        <legend>Your Contact Details</legend>

        <div>
            <label class="form-label" for="contact_details_first_name">First Name</label>
            <input id="contact_details_first_name" type="text" name="contact_first_name" value="<?= $contact['first_name'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_last_name">Last Name</label>
            <input id="contact_details_last_name" type="text" name="contact_last_name" value="<?= $contact['last_name'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_phone">Phone</label>
            <input id="contact_details_phone" type="text" name="contact_phone" value="<?= $contact['phone'] ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_email">Email</label>
            <input id="contact_details_email" type="text" name="contact_email" value="<?= $contact['email'] ?>" />
        </div>
    </fieldset>

    <fieldset class="form-block">
        <legend>Notes</legend>
        <label for="contact_details_notes" class="hide">Notes</label>
        <textarea id="contact_details_notes" name="notes"><?= $customer['notes'] ?></textarea>
    </fieldset>

    <fieldset class="form-block">
        <legend>Billing Contact Details</legend>

        <div>
            <label class="form-label" for="contact_details_billing_first_name">First Name</label>
            <input id="contact_details_billing_first_name" type="text" name="billing_first_name" value="<?= isset($billing_contact['first_name']) ? $billing_contact['first_name'] : '' ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_billing_last_name">Last Name</label>
            <input id="contact_details_billing_last_name" type="text" name="billing_last_name" value="<?= isset($billing_contact['last_name']) ? $billing_contact['last_name'] : '' ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_billing_phone">Phone</label>
            <input id="contact_details_billing_phone" type="text" name="billing_phone" value="<?= isset($billing_contact['phone']) ? $billing_contact['phone'] : '' ?>" />
        </div>

        <div>
            <label class="form-label" for="contact_details_billing_email">Email</label>
            <input id="contact_details_billing_email" type="text" name="billing_email" value="<?= isset($billing_contact['email']) ? $billing_contact['email'] : '' ?>" />
        </div>
    </fieldset>
    <button type="button" id="update_customer_and_pay">Save</button>
</form>
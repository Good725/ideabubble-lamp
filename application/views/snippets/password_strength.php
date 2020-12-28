<div class="password-info hidden">
    <div>
        <button type="button" class="button--plain password-info-close">
            <span class="icon_close" aria-hidden="true"></span>
        </button>

        <div class="password-strength">
            <h4><?= __('Password strength: $1', array('$1' => '<span class="password-strength-result">'.__('Weak').'</span>')) ?></h4>

            <div class="password-strength-meter">
                <span class="password-strength-meter-weak"          data-label="<?= __('Weak')   ?>"></span>
                <span class="password-strength-meter-good hidden"   data-label="<?= __('Good')   ?>"></span>
                <span class="password-strength-meter-strong hidden" data-label="<?= __('Strong') ?>"></span>
            </div>
        </div>
    </div>

    <ul>
        <li class="password-strength-length  invalid"><?= __('Be at least eight characters') ?></li>
        <li class="password-strength-letter  invalid"><?= __('Have at least one lower case letter') ?></li>
        <li class="password-strength-capital invalid"><?= __('Have at least one upper case letter') ?></li>
        <li class="password-strength-number  invalid"><?= __('Have at least one number') ?></li>
        <li class="password-strength-nospecial  invalid"><?= __('No special character') ?></li>
    </ul>
</div>
<p>Hi <?= (isset($form['first_name']) AND ! empty($form['first_name'])) ? $form['first_name'] : $form['name'] ?>,</p>

<p>Your username is <?= $form['email'] ?></p>

<p>To initiate the password reset process for your IB Platform account, please <a href="<?=URL::site().'admin/login/reset_password_form/'.$form['validation'];?>">click here</a>.</p>

<p>If clicking the link above does not work, please copy and paste the full URL below into a new browser window:</p>

<p><a href="<?= URL::site().'admin/login/reset_password_form/'.$form['validation'] ?>"><?= URL::site().'admin/login/reset_password_form/'.$form['validation'] ?></a></p>

<p>If you have received this email in error, it is likely that another user entered your email address by mistake, while
trying to reset a password. If you did not initiate the request, you do not need to take any further action and can
safely disregard his email.</p>

<p>If you experience any difficulties during this process, please contact us at support@ideabubble.ie</p>

<p>The Idea Bubble Team</p>
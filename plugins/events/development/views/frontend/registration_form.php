<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?= __('Register') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

    <script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
    <style>
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .start-selling-button {
            font-size: 20px;
        }
    </style>
</head>
<body>
<div class="container login-form-container">
    <div class="modal show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body form-horizontal">
                    <section class="form-section active">
                        <form method="post">
                            <?=$alert ? '<p>' . $alert . '</p>' : ''?>
                            <div class="form-group">
                                <div class="col-sm-8">
                                    <h2><?= __('Sign Up') ?></h2>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="sr-only" for="event-registration-first_name"><?= __('First Name') ?> *</label>
                                    <input type="text" class="form-control" id="event-registration-first_name" name="first_name" placeholder="<?= __('First Name') ?>" required="required" />
                                </div>

                                <div class="col-sm-6">
                                    <label class="sr-only" for="event-registration-last_name"><?= __('Last Name') ?> *</label>
                                    <input type="text" class="form-control" id="event-registration-last_name" name="last_name" placeholder="<?= __('Last Name') ?>" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="sr-only" for="event-registration-email"><?= __('Email') ?> *</label>
                                    <input type="text" class="form-control" id="event-registration-email" name="email" placeholder="<?= __('Email') ?>" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="sr-only" for="event-registration-password"><?= __('Password') ?> *</label>
                                    <input type="password" class="form-control" id="event-registration-password" name="password" placeholder="<?= __('Password') ?>" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-lg btn-default continue-button" id="action-register" name="action" value="register"><?= __('Sign Up Now') ?></button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    // Adding social media links
    $('#event-registration-social-link-btn').on('click', function()
    {
        var social_media = $('#event-registration-social-link-selector').val();
        var count = $('.event-registration-social-link').length - 1;

        var $new_block = $('#event-registration-social-link-template').clone().removeAttr('id');
        $new_block.find('input')
            .attr('placeholder', 'Enter '+social_media+' URL')
            .attr('name', 'organizer['+(social_media.toLowerCase())+']')
        ;

        $('#event-registration-social-links').append($new_block);
        $new_block.removeClass('hidden');
    });

    $('#event-registration-organizer').on('keyup change', function()
    {
        $('#event-registration-url').val(this.value.replace(/[^a-z0-9]+/g, '-').toLowerCase());
        if (this.xtimeout) {
            clearTimeout(this.xtimeout);
        }
        $("#action-register").prop("disabled", true);
        this.xtimeout = setTimeout(
            function(){
                $("#event-registration-url").change();
            },
            400
        );
    });

    $("#event-registration-url").on("change", function(){
        $("#action-register").prop("disabled", true);
        $.post(
            "/check_organizer_url",
            {url: $('#event-registration-url').val()},
            function (response) {
                if (response.exists) {
                    $('#event-registration-url').val(response.suggestion);
                }
                $("#action-register").prop("disabled", false);
            }
        );
    });
</script>
</body>
</html>
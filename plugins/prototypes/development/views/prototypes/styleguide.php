<?php
$contexts = ['primary' => 'Primary', 'default' => 'Default', 'success' => 'Success', 'info' => 'Info', 'warning' => 'Warning', 'danger' => 'Danger'];
$skins = Model_Settings::cms_skin_options(Settings::instance()->get('cms_skin'), true);
unset($skins['01']);

$sections = [
    'alerts'       => 'Alerts',
    'badges'       => 'Badges',
    'breadcrumbs'  => 'Breadcrumbs',
    'buttons'      => 'Buttons',
    'dashboard_icons' => 'Dashboard icons',
    'dropdowns'    => 'Dropdowns',
    'form_fields'  => 'Form fields',
    'headings'     => 'Headings',
    'login'        => 'Log in',
    'modals'       => 'Modals',
    'reports'      => 'Reports',
    'tables'       => 'Tables',
    'tabs'         => 'Tabs',
    'text'         => 'Text',
    'twitter_feed' => 'Twitter feed'
];

$section_classes = 'styleguide-section mb-4';
?>

<link rel="stylesheet" href="<?= URL::get_engine_assets_base()?>css/validation.css" />
<link rel="stylesheet" href="/engine/plugins/reports/css/reports.css" />
<link rel="stylesheet" href="/engine/plugins/contacts3/css/contacts.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/dark-bottom.css" rel="stylesheet" />

<style>
    #styleguide-section-alerts .alert-stay {
        position: relative;
        top: 0;
        left: 0;
    }

    #styleguide-section-modals .modal {
        display: block;
        position: relative;
    }
    body {
        counter-reset: h2-number h3-number;
    }

    h2.numbered-header:before {
        counter-increment: h2-number;
        content: counter(h2-number)'. ';
    }

    .styleguide-section {
        counter-reset: h3-number;
    }

    h3.numbered-header:before {
        counter-increment: h3-number;
        content: counter(h2-number)'.'counter(h3-number)'. ';
    }
</style>

<div class="border bg-white p-2 mr-1" style="position: fixed; right: 0; z-index: 1; width: 220px;">
    <p>Preview this page in another theme.</p>

    <ul class="list-unstyled">
        <?php foreach ($skins as $key => $label): ?>
            <li>
                <button type="button" class="btn-link p-0 styleguide-skin-btn" data-skin="<?= $key ?>"><?= $label ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<ol class="border mb-4 p-2 d-inline-block bg-white" id="styleguide-toc">
    <?php foreach ($sections as $key => $label): ?>
        <li class="ml-4 my-1">
            <a href="#styleguide-section-<?= $key ?>"><?= htmlentities($label) ?></a>
        </li>
    <?php endforeach; ?>
</ol>

<?php foreach ($sections as $section_key => $section_label): ?>
    <section class="styleguide-section mb-4" id="styleguide-section-<?= $section_key ?>">
        <h2 class="numbered-header"><?= htmlspecialchars($section_label) ?></h2>

        <?php include 'styleguide/'.$section_key.'.php'; ?>
    </section>
<?php endforeach; ?>

<script>
    $('.styleguide-skin-btn').on('click', function() {
        var skin = this.getAttribute('data-skin');

        // Update the <link /> to use the selected stylesheet
        var link_tag = $('link[href*="/css/styles.css"]')[0]; // Should use an ID selector, but a hack was necessary to avoid a conflict
        link_tag.href = link_tag.href.replace(/\/[^\/]*\/css/, '/'+skin+'/css');

        $('body').toggleClass('theme-02', (skin == '02'));

        // Update <iframe /> URLS to specify the selected stylesheet
        var iframes = document.getElementsByTagName('iframe');

        for (var i = 0; i < iframes.length; i++) {
            var new_src = iframes[i].src.replace(/usetheme=[^\&]*/, 'usetheme='+skin);
            if (iframes[i].src != new_src) {
                iframes[i].src = new_src;
            }
        }

        <?php // This needs a proper callback to ensure it runs, after the theme has been applied, but I don't have time to set one up, right now ?>
        setTimeout(display_font_styles, 2000);
    });

    $('#style-guide-table-clientside, #style-guide-table-serverside').on('click', 'tr', function(ev) {
        if (!$(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0]) {
            $(this).parents('table').find('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });

    $(document).ready(function() {
        display_font_styles();
        $('#style-guide-table-serverside').ib_serverSideTable('/admin/prototypes/ajax_states_datatable');
    });

    function display_font_styles()
    {
        var font, font_size, font_weight;
        $('.styleguide-font-styles').each(function() {
            font = $(this).css('font-family').split(', ')[0].replace(/["|']/g, '');
            font_size = $(this).css('font-size');
            font_weight = $(this).css('font-weight');

            $(this).text(' - '+font+' '+font_size+' '+font_weight);
        });
    }

</script>
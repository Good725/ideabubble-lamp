<div class="survey-input-wrapper">
    <?php $options = $question->answer->options->find_all_published(); ?>

    <div class="survey-select">
        <?php
        $option_html = html::optionsFromRows('id', 'label', $options, $response, ['value' => '', 'label' => '-- Please select--']);
        $name = (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id;
        $class = 'survey-input'.($question->required ? ' validate[required]' : '');
        echo Form::ib_select(null, $name, $option_html, null, ['class' => $class, 'id' => 'temporary_'.$question->id]);;
        ?>
    </div>
</div>

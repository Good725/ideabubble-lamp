<?php
echo View::factory('/snippets/modal')
    ->set('title',  'Regular modal')
    ->set('body',   'Text goes here.')
    ->set('footer', '<button type="button" class="btn btn-primary btn-lg">OK</button> <button type="button" class="btn btn-cancel btn-lg">Cancel</button>');

echo View::factory('/snippets/modal')
    ->set('size',   'sm')
    ->set('title',  'Small modal')
    ->set('body',   'Text goes here.')
    ->set('footer', '<button type="button" class="btn btn-primary btn-lg">OK</button>');

echo View::factory('/snippets/modal')
    ->set('size',   'lg')
    ->set('title',  'Large modal')
    ->set('body',   'Text goes here.')
    ->set('footer', '<button type="button" class="btn btn-primary btn-lg">OK</button> <button type="button" class="btn btn-cancel btn-lg">Cancel</button>');

?>
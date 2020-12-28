<div class="newsHolder">
    <div class="box-block">
    <?php foreach ($items as $elem => $val): ?>
        <article class="box">
            <header class="title <?=isset($colors[$elem])? $colors[$elem]:'default';?>"><a href="/courses/<?php echo IbHelpers::generate_friendly_url($val['category']) . '.html';?>"><h3 class="arrow"><?=$val['category']?></h3></a></header>
            <section class="box-content">
                <?=$val['description']?>
            </section>
        </article>
    <?php endforeach; ?>

    </div>
</div>




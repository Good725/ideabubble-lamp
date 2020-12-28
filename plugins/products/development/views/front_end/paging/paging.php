<form action="<?= URL::base(); ?>search.html" method="get" class="paging-bl">
    <?= $paging_button; // display paging button ?>

    <?php
    if (@$featured_only) {
    ?>
    <input type="hidden" name="featured_only" value="1">
    <?php
    }
    ?>
    <input type="hidden" name="price" value="<?= $price; ?>"><?
    foreach ($categories as $category_id => $name) { ?>
        <input type="hidden" name="category[<?= $category_id; ?>]" value="<?= $name; ?>"><?
    }
    foreach ($sub_categories as $category_id => $sub_category) {
        foreach ($sub_category as $sub_category_id => $sub_category_name) { ?>
            <input type="hidden" name="sub-category[<?= $category_id; ?>][<?= $sub_category_id; ?>]" value="<?= $sub_category_name; ?>"><?
        }
    }
    foreach ($brands as $brand_id => $name) { ?>
        <input type="hidden" name="brand[<?= $brand_id; ?>]" value="<?= $name ?>"><?
    }
    foreach ($distributors as $distributor_id => $name) { ?>
        <input type="hidden" name="distributor[<?= $distributor_id; ?>]" value="<?= $name ?>"><?
    }?>
</form>
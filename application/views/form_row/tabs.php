<ul class="nav nav-tabs">
    <?php foreach ($tabs as $tab_name => $tab): ?>
        <li
            <?= ($tab['active']) ? ' class="active"' : '' ?>
            <?= $tab['id'] ? ' id="' . $tab['id'] . '"' : '' ?>
            <?= $tab['class'] ? ' class="' . $tab['class'] . '"' : '' ?>
        >
            <a
                href="#<?= $form->id ?>-tab-<?= $form->string_to_id($tab_name) ?>"
                data-toggle="tab"
            ><?= htmlspecialchars($tab_name) ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <?php foreach ($tabs as $tab_name => $tab): ?>
        <div class="tab-pane<?= $tab['active'] ? ' active' : '' ?>" id="<?= $form->id ?>-tab-<?= $form->string_to_id($tab_name) ?>">
            <?= $tab['content'] ?>
        </div>
    <?php endforeach; ?>
</div>
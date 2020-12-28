<p>Standard</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel'] as $key => $label): ?>
        <button class="btn btn-<?= $key ?>"><?= $label ?></button>
    <?php endforeach; ?>
</p>

<p>Outline</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel']  as $key => $label): ?>
        <button class="btn btn-outline-<?= $key ?>"><?= $label ?></button>
    <?php endforeach; ?>
</p>

<p>Disabled</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel']  as $key => $label): ?>
        <button class="btn btn-<?= $key ?>" disabled><?= $label ?></button>
    <?php endforeach; ?>
</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel']  as $key => $label): ?>
        <button class="btn btn-outline-<?= $key ?>" disabled><?= $label ?></button>
    <?php endforeach; ?>
</p>

<p>Large</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel']  as $key => $label): ?>
        <button class="btn btn-lg btn-<?= $key ?>"><?= $label ?></button>
    <?php endforeach; ?>
</p>

<p>
    <?php foreach ($contexts + ['cancel' => 'Cancel']  as $key => $label): ?>
        <button class="btn btn-lg btn-outline-<?= $key ?>"><?= $label ?></button>
    <?php endforeach; ?>
</p>
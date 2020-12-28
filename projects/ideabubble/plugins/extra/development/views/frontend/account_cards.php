<h2>Your Payment Cards</h2>
<form action="/frontend/extra/delete_card" method="post">
	<input type="hidden" name="customer_id" value="<?=$customer['id']?>" />
    <table class="zebra cards-table">
        <thead>
            <tr>
                <th scope="col">Card Number#</th>
                <th scope="col">Expires</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($cards)) {
                foreach ($cards as $card) {
            ?>
                <tr data-card-id="<?= $card['id']; ?>">
                    <td><?= $card['card_number'] ?></td>
                    <td><?= date('m/Y', strtotime($card['expdate'])) ?></td>
                    <td><input type="checkbox" name="card_delete[]" value="<?= $card['id'] ?>"/></td>
                </tr>
            <?php
                }
            } else {
            ?>
                <tr><td colspan="3">No Card</td></tr>
            <?php
            }
			?>
        </tbody>
		<tfoot>
        <?php
        if (count($cards)) {
        ?>
        <tr>
            <th colspan="3">
                <button type="submit">Delete Selected Cards</button>
            </th>
        </tr>
        <?php
        }
        ?>
		</tfoot>
    </table>
</form>
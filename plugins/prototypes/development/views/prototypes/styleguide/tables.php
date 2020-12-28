<p>The below tables are initialised differently, but <em>should</em> look the same.</p>

<h3 class="numbered-header">Client-side</h3>

<div class="clearfix">
    <table class="table dataTable dataTable-collapse" id="style-guide-table-clientside">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Code</th>
            <th scope="col">Name</th>
            <th scope="col">Capital</th>
            <th scope="col">Publish</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>

        <?php $states = [[ "abbr"=>"AL", "name"=>"Alabama", "capital"=>"Montgomery"], [ "abbr"=>"AK", "name"=>"Alaska", "capital"=>"Juneau"], [ "abbr"=>"AZ", "name"=>"Arizona", "capital"=>"Phoenix"], [ "abbr"=>"AR", "name"=>"Arkansas", "capital"=>"Little Rock"], [ "abbr"=>"CA", "name"=>"California", "capital"=>"Sacramento"], [ "abbr"=>"CO", "name"=>"Colorado", "capital"=>"Denver"], [ "abbr"=>"CT", "name"=>"Connecticut", "capital"=>"Hartford"], [ "abbr"=>"DE", "name"=>"Delaware", "capital"=>"Dover"], [ "abbr"=>"FL", "name"=>"Florida", "capital"=>"Tallahassee"], [ "abbr"=>"GA", "name"=>"Georgia", "capital"=>"Atlanta"], [ "abbr"=>"HI", "name"=>"Hawaii", "capital"=>"Honolulu"], [ "abbr"=>"ID", "name"=>"Idaho", "capital"=>"Boise"], [ "abbr"=>"IL", "name"=>"Illinois", "capital"=>"Springfield"], [ "abbr"=>"IN", "name"=>"Indiana", "capital"=>"Indianapolis"], [ "abbr"=>"IA", "name"=>"Iowa", "capital"=>"Des Moines"], [ "abbr"=>"KS", "name"=>"Kansas", "capital"=>"Topeka"], [ "abbr"=>"KY", "name"=>"Kentucky", "capital"=>"Frankfort"], [ "abbr"=>"LA", "name"=>"Louisiana", "capital"=>"Baton Rouge"], [ "abbr"=>"ME", "name"=>"Maine", "capital"=>"Augusta"], [ "abbr"=>"MD", "name"=>"Maryland", "capital"=>"Annapolis"], [ "abbr"=>"MA", "name"=>"Massachusetts", "capital"=>"Boston"], [ "abbr"=>"MI", "name"=>"Michigan", "capital"=>"Lansing"], [ "abbr"=>"MN", "name"=>"Minnesota", "capital"=>"Saint Paul"], [ "abbr"=>"MS", "name"=>"Mississippi", "capital"=>"Jackson"], [ "abbr"=>"MO", "name"=>"Missouri", "capital"=>"Jefferson City"], [ "abbr"=>"MT", "name"=>"Montana", "capital"=>"Helana"], [ "abbr"=>"NE", "name"=>"Nebraska", "capital"=>"Lincoln"], [ "abbr"=>"NV", "name"=>"Nevada", "capital"=>"Carson City"], [ "abbr"=>"NH", "name"=>"New Hampshire", "capital"=>"Concord"], [ "abbr"=>"NJ", "name"=>"New Jersey", "capital"=>"Trenton"], [ "abbr"=>"NM", "name"=>"New Mexico", "capital"=>"Santa Fe"], [ "abbr"=>"NY", "name"=>"New York", "capital"=>"Albany"], [ "abbr"=>"NC", "name"=>"North Carolina", "capital"=>"Raleigh"], [ "abbr"=>"ND", "name"=>"North Dakota", "capital"=>"Bismarck"], [ "abbr"=>"OH", "name"=>"Ohio", "capital"=>"Columbus"], [ "abbr"=>"OK", "name"=>"Oklahoma", "capital"=>"Oklahoma City"], [ "abbr"=>"OR", "name"=>"Oregon", "capital"=>"Salem"], [ "abbr"=>"PA", "name"=>"Pennsylvania", "capital"=>"Harrisburg"], [ "abbr"=>"RI", "name"=>"Rhode Island", "capital"=>"Providence"], [ "abbr"=>"SC", "name"=>"South Carolina", "capital"=>"Columbia"], [ "abbr"=>"SD", "name"=>"South Dakota", "capital"=>"Pierre"], [ "abbr"=>"TN", "name"=>"Tennessee", "capital"=>"Nashville"], [ "abbr"=>"TX", "name"=>"Texas", "capital"=>"Austin"], [ "abbr"=>"UT", "name"=>"Utah", "capital"=>"Salt Lake City"], [ "abbr"=>"VT", "name"=>"Vermont", "capital"=>"Montpelier"], [ "abbr"=>"VA", "name"=>"Virginia", "capital"=>"Richmond"], [ "abbr"=>"WA", "name"=>"Washington", "capital"=>"Olympia"], [ "abbr"=>"WV", "name"=>"West Virginia", "capital"=>"Charleston"], [ "abbr"=>"WI", "name"=>"Wisconsin", "capital"=>"Madison"], [ "abbr"=>"WY", "name"=>"Wyoming", "capital"=>"Cheyenne"]]; ?>

        <tbody>
        <?php foreach ($states as $key => $state): ?>
            <tr>
                <td data-label="ID"><?= $key+1?></td>
                <td data-label="Code"><?= $state['abbr'] ?></td>
                <td data-label="Name"><?= htmlentities($state['name']) ?></td>
                <td data-label="Capital"><?= htmlentities($state['capital']) ?></td>
                <td data-label="Publish">
                    <label class="checkbox-icon">
                        <input type="checkbox" checked />
                        <span class="checkbox-icon-unchecked icon-ban-circle"></span>
                        <span class="checkbox-icon-checked icon-check"></span>
                    </label>
                </td>
                <td data-label="Actions">
                    <?php
                    echo View::factory('snippets/btn_dropdown')
                        /* Replace all this with ->set('type', 'actions') when the necessary branch has been merged */
                        ->set('title',         ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                        ->set('sr_title',      'Actions')
                        ->set('btn_type',      'outline-primary')
                        ->set('options_align', 'right')
                        /**/
                        ->set('options',       [
                            ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-pencil"></span> Edit']],
                            ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-clone"></span> Clone']],
                            ['type' => 'button', 'title'  => ['html' => true, 'text' => '<span class="icon-close"></span> Delete']]
                        ]);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3 class="numbered-header">Server-side</h3>

<div class="clearfix">
    <table class="table dataTable" id="style-guide-table-serverside">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Code</th>
            <th scope="col">Name</th>
            <th scope="col">Capital</th>
            <th scope="col">Publish</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>

        <tbody></tbody>
    </table>
</div>
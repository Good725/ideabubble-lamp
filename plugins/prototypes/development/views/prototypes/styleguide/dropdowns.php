<p><span class="text-danger">Raw HTML is embedded wherever these are used. We need to template the input.</span></p>

<p>Dropdowns can be as wide as needed to fit the area or as thin as needed to contain the text.</p>

<div class="row gutters" style="padding-bottom: 8rem;">
    <div class="col-sm-4">
        <p>Options are links</p>

        <div class="dropdown open">
            <button type="button" class="btn btn-default button--full form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                View <span class="btn-dropdown-selected_text"></span>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right" style="width: 100%;">
                <li>
                    <a href="#">Option 1</a>
                </li>
                <li>
                    <a href="#">Option 2</a>
                </li>
                <li>
                    <a href="#">Option 3</a>
                </li>
                <li>
                    <a href="#">Option 4</a>
                </li>
            </ul>
        </div>

        <p>Open new pages</p>
    </div>

    <div class="col-sm-4">
        <p>Options are buttons</p>

        <div class="dropdown open">
            <button type="button" class="btn btn-default button--full form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                View <span class="btn-dropdown-selected_text"></span>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right" style="width: 100%;">
                <li>
                    <button type="button">Option 1</button>
                </li>
                <li>
                    <button type="button">Option 2</button>
                </li>
                <li>
                    <button type="button">Option 3</button>
                </li>
                <li>
                    <button type="button">Option 4</button>
                </li>
            </ul>
        </div>

        <p>Perform actions when clicked</p>
    </div>

    <div class="col-sm-4">
        <p>Options are radio buttons</p>

        <div class="dropdown open">
            <button type="button" class="btn btn-default button--full form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                View <span class="btn-dropdown-selected_text"></span>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right" style="width: 100%;">
                <li>
                    <label class="radio-bullet">
                        <input type="radio" class="dropdown-menu-radio timetable-planner-view" name="view" value="<?= $key ?>" />
                        <span>Option 1</span>
                    </label>
                </li>
                <li>
                    <label class="radio-bullet">
                        <input type="radio" class="dropdown-menu-radio timetable-planner-view" name="view" value="<?= $key ?>" />
                        <span>Option 2</span>
                    </label>
                </li>
                <li>
                    <label class="radio-bullet">
                        <input type="radio" class="dropdown-menu-radio timetable-planner-view" name="view" value="<?= $key ?>" />
                        <span>Option 3</span>
                    </label>
                </li>
                <li>
                    <label class="radio-bullet">
                        <input type="radio" class="dropdown-menu-radio timetable-planner-view" name="view" value="<?= $key ?>" />
                        <span>Option 4</span>
                    </label>
                </li>
            </ul>
        </div>

        <p>Selected option remains highlighted</p>
    </div>
</div>

<p>Contextual colours</p>

<div class="mb-2">
    <?php foreach ($contexts as $key => $label): ?>
        <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-<?= $key ?> form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $label ?>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right">
                <li><a href="#">Option 1</a></li>
                <li><a href="#">Option 2</a></li>
                <li><a href="#">Option 3</a></li>
                <li><a href="#">Option 4</a></li>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

<div class="mb-2">
    <?php foreach ($contexts as $key => $label): ?>
        <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-outline-<?= $key ?> form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $label ?>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu pull-right">
                <li><a href="#">Option 1</a></li>
                <li><a href="#">Option 2</a></li>
                <li><a href="#">Option 3</a></li>
                <li><a href="#">Option 4</a></li>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

<div class="mb-2">
    <p>Actions dropdown (This needs to be templated and replaced with the HTML for the "primary outline" above.)</p>

    <div class="action-btn">
        <a class="btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="icon-ellipsis-h" aria-hidden="true"></span>
        </a>
        <ul class="dropdown-menu">
            <li><button type="button" class="view">Option 1</button></li>
            <li><button type="button" class="view">Option 2</button></li>
            <li><button type="button" class="view">Option 3</button></li>
        </ul>
    </div>
</div>
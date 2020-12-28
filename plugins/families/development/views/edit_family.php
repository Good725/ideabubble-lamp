<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<?php
if (isset($data['family'])) {
    $family = $data['family'];
} else {
    $family = array('id' => 'New', 'family' => 'New');
}
?>
<div class="expanded-section" id="edit_family">
    <div id="edit_family_heading" class="edit_heading">
		<div class="edit_heading-row">
			<h2 class="edit_heading-title">Family <strong><?=@$family['family'] ?> <span class="span_client_id">#<?=@$family['id'] ?></span></strong></h2>
			<div class="flags"></div>

			<?php if (is_numeric(@$family['id'])) { ?>
				<div class="heading-buttons right">
					<div class="btn-group">
						<button type="button" class="btn btn-lg dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
						<ul class="dropdown-menu pull-right">
							<li><a href="#" class="add_new_family_member_link">New Member</a></li>
							<?php if (1): ?>
								<li role="presentation" class="divider"></li>
								<li><a href="#" class="text-danger" data-action="delete_family" data-original-title="Delete Family" data-content="Delete the family and remove the association to the members. Family members will be listed with No Family">Delete</a></li>
							<?php endif; ?>
							<?php if (Auth::instance()->has_access('contacts2_edit') && is_numeric(@$family['id'])) { ?>
								<li><a class="add_note family">Add Note</a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
		</div>
    </div>

    <div class="expand-section-tabs"">
        <div id="family_header_buttons"></div>
        <ul class="edit_heading-tabs nav nav-tabs nav-tabs-family">
            <li class="active"><a data-toggle="tab" href="#family-details-tab"  >Details</a></li>
            <?php if (@$family['id'] && Auth::instance()->has_access('contacts2_edit')) { ?>
            <li><a data-toggle="tab" href="#family-notes-tab"    >Notes</a></li>
            <?php } ?>
            <?php
            foreach (Model_Families::getExtentions() as $extention) {
                foreach ($extention->getTabs($family) as $xTab) {
                    ?>
                    <li><a data-toggle="tab" href="#family-extention-<?= $xTab['name'] ?>-tab"><?= $xTab['title'] ?></a></li>
                    <?php
                }
            }
            ?>
			<!-- <li><a data-toggle="tab" href="#family-accounts-tab" >Accounts</a></li> -->
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="family-details-tab">
            <div class="alert-area"></div>
            <div class="content-area">
                <?php include 'family_details.php'; ?>

                <?php if (is_numeric(@$family['id'])) { ?>
                <div class="heading-buttons right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-lg dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="add_new_family_member_link">Add New Member</a></li>
                        </ul>
                    </div>
                </div>

				<div class="col-sm-12">
					<h3>Family Members</h3>
				</div>
                <?php include 'family_members.php'; ?>
                <?php } ?>
            </div>
        </div>

        <?php if (is_numeric(@$family['id'])) { ?>
        <div class="tab-pane" id="family-notes-tab"    >
            <div class="alert-area"></div>
            <div class="content-area"><?=View::factory('list_notes2', array('notes' => @$family['notes']))?></div>
        </div>

        <?php } ?>

        <?php
        foreach (Model_Families::getExtentions() as $extention) {
            foreach ($extention->getTabs($family) as $xTab) {
                ?>
                <div class="tab-pane" id="family-extention-<?= $xTab['name'] ?>-tab">
                    <div class="alert-area"><?= (isset($alert)) ? $alert : ''; ?></div>
                    <div class="content-area"><?= View::factory($xTab['view'], array('family' => $family, 'data' => $extention->getData($family))); ?></div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>



<?php if ($contact) { ?>
	<?=$contact?>
<?php } ?>


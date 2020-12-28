<div class="btn-group">
	<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Select Action <span class="caret"></span></button>
	<ul class="dropdown-menu pull-right">
		<!--
		<li><a href="/admin/reports/add_edit_report"><i class="icon-plus"></i> Add Report</a></li>
		<li><a href="#" id="generate_csv"><i class="icon-download-alt"></i> Download CSV</a></li>
		<li><a href="#" id="print_report"><i class="icon-print"></i> Print Report</a></li>
		-->
        <?php if (Auth::instance()->has_access('reports_edit')): ?>
            <li>
                <a href="/admin/reports/add_edit_report">
                    <span class="icon-plus"></span> Add report
                </a>
            </li>

            <?php if (!empty($id)): ?>
                <?php if (Request::$current->action() == 'read'): ?>
                    <li>
                        <a href="/admin/reports/add_edit_report/<?= $id ?>">
                            <span class="icon-pencil"></span> Edit report
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="/admin/reports/read/<?= $id ?>">
                            <span class="icon-eye"></span> View report
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
		
		<?php if ($id): ?>
			<li>
				<a href="#" class="get_report_option" data-action="download" data-format="csv">
					<span class="icon-download-alt"></span> Download as CSV
				</a>
			</li>
				<!-- <li>
				<a href="#" class="get_report_option" data-action="download" data-format="xls">
					<span class="icon-download-alt"></span> Download as XLS
				</a>
			</li> -->
			<li>
				<a href="#" class="get_report_option" data-action="email" data-format="csv">
					<span class="icon-envelope-alt"></span> Email as CSV
				</a>
			</li>
				<!-- <li>
				<a href="#" class="get_report_option" data-action="email" data-format="xls">
					<span class="icon-envelope-alt"></span> Email as XLS
				</a>
			</li> -->
			<li>
				<a href="#" id="print_report">
					<span class="icon-print"></span> Print report
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>
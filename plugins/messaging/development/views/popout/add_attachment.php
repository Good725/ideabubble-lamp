	<div class="mail-title">
		<h2>Add an Attachment</h2>
        <a href="javascript:void(0)" class="basic_close"><span class="fa icon-times" aria-hidden="true"></span></a>
	</div>

	<div class="padding10">
		<div class="file-detail">
            <div class="alert-area"></div>
            <div class="sub-title">
                <h3>Add your file below</h3>
            </div>
            <div class="drag_and_drop_area">
                <div class="upload_text">
                    <p class="dnd_notice">
                        <i class="fa icon-cloud-download" aria-hidden="true"></i>Drag and drop files here
                    </p>

                    <div class="upload-button">
                        <label class="btn btn-primary">
                            <span class="fa icon-picture-o" aria-hidden="true"></span>
                            <span>Upload</span>
                            <input type="file" class="sr-only" id="messaging-sidebar-add_photo" name="attachment" />
                        </label>
                    </div>

                    <?php if (Auth::instance()->has_access('messaging_see_under_developed_features')): ?>
                        <div class="upload-button">
                            <label class="btn btn-default">
                                <span class="fa icon-file-text" aria-hidden="true"></span>
                                <span>Browse</span>
                            </label>
                        </div>

                        <div class="upload-button">
                            <button type="button" class="btn btn-default">
                                <span class="fa icon-paperclip" aria-hidden="true"></span>
                                <span>Url</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <p class="note">Maximum Upload File Size: 25&nbsp;MB</p>
                </div>
            </div>


            <div class="sub-title">
                <h3>View your files</h3>
            </div>
            <div class="table-scroll">
                <table class="table-border" id="messaging-sidebar-attachments-list" style="display: none;">
                    <tbody></tbody>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tfoot class="hidden" id="messaging-sidebar-attachments-list-template">
                        <tr>
                            <td>
                                <button type="button" data-src="" class="btn-link popup-btn messaging-sidebar-attachment-icon" >
                                    <img  class="attachment-icon attachment-icon--image    hidden" src="" alt="" />
                                    <span class="attachment-icon attachment-icon--document hidden icon-file-text-o"></span>
                                    <span class="attachment-icon attachment-icon--pdf      hidden icon-file-pdf-o"></span>
                                    <span class="attachment-icon attachment-icon--default  hidden icon-file-o"></span>
                                </button>

                                <span class="messaging-sidebar-attachment-name"></span>

                                <input type="hidden" class="messaging-sidebar-attachment-url" />
                            </td>
                            <td>
                                <button type="button" class="btn-link messaging-sidebar-attachment-remove">
                                    <span class="fa icon-times" aria-hidden="true"></span>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php /*
			<div id="tab2" class="tabs-content">
				<div class="sub-title">
					<h3>Add your link below</h3>
				</div>
				<div class="drag_and_drop_area">
					<div class="upload_text">
						<p class="dnd_notice">
							Add a link to a Message
						</p>
						<div class="upload-button">
							<button type="button" class="btn cyan-bg">
							   <i class="fa icon-folder" aria-hidden="true"></i>
								<span>Upload</span>
							</button>
							<input multiple="multiple" type="file">
						</div>
						<div class="upload-button">
							<button type="button" class="btn black-bg">
								<i class="fa icon-file-text" aria-hidden="true"></i>
								<span>Browse</span>
							</button>
							<input multiple="multiple" type="file">
						</div>
					</div>
				</div>
				<div class="sub-title">
					<h3>View your files</h3>
				</div>
				<div class="table-scroll">
					<table class="table-border">
						<tr>
							<th>Name</th>
							<th>Link to</th>
							<th>Action</th>
						</tr>
						<tr>
							<td><a href="#">https://ideabubble.atlassian.net/...</a></td>
							<td>
								<div class="action-btn">
									<a href="#" class="pullBtn"><i class="fa icon-ellipsis-h" aria-hidden="true"></i></a>
									<ul class="toggle-box">
										<li><a href="#">Claim</a></li>
										<li><a href="#">Policy</a></li>
										<li><a href="#">Contacts</a></li>
										<li><a href="#">Document</a></li>
									</ul>
								</div>
							</td>
							<td><a href="#"><i class="fa icon-times" aria-hidden="true"></i></a></td>
						</tr>
						<tr>
							<td><a href="#">https://ideabubble.atlassian.net/...</a></td>
							<td>
								<div class="action-btn">
									<a href="#" class="pullBtn"><i class="fa icon-ellipsis-h" aria-hidden="true"></i></a>
									<ul class="toggle-box">
										<li><a href="#">Claim</a></li>
										<li><a href="#">Policy</a></li>
										<li><a href="#">Contacts</a></li>
										<li><a href="#">Document</a></li>
									</ul>
								</div>
							</td>
							<td><a href="#"><i class="fa icon-times" aria-hidden="true"></i></a></td>
						</tr>
						<tr>
							<td><a href="#">https://ideabubble.atlassian.net/...</a></td>
							<td>
								<div class="action-btn">
									<a href="#" class="pullBtn"><i class="fa icon-ellipsis-h" aria-hidden="true"></i></a>
									<ul class="toggle-box">
										<li><a href="#">Claim</a></li>
										<li><a href="#">Policy</a></li>
										<li><a href="#">Contacts</a></li>
										<li><a href="#">Document</a></li>
									</ul>
								</div>
							</td>
							<td><a href="#"><i class="fa icon-times" aria-hidden="true"></i></a></td>
						</tr>
						<tr>
							<td><a href="#">https://ideabubble.atlassian.net/...</a></td>
							<td>
								<div class="action-btn">
									<a href="#" class="pullBtn"><i class="fa icon-ellipsis-h" aria-hidden="true"></i></a>
									<ul class="toggle-box">
										<li><a href="#">Claim</a></li>
										<li><a href="#">Policy</a></li>
										<li><a href="#">Contacts</a></li>
										<li><a href="#">Document</a></li>
									</ul>
								</div>
							</td>
							<td><a href="#"><i class="fa icon-times" aria-hidden="true"></i></a></td>
						</tr>
						<tr>
							<td><a href="#">https://ideabubble.atlassian.net/...</a></td>
							<td>
								<div class="action-btn">
									<a href="#" class="pullBtn"><i class="fa icon-ellipsis-h" aria-hidden="true"></i></a>
									<ul class="toggle-box">
										<li><a href="#">Claim</a></li>
										<li><a href="#">Policy</a></li>
										<li><a href="#">Contacts</a></li>
										<li><a href="#">Document</a></li>
									</ul>
								</div>
							</td>
							<td><a href="#"><i class="fa icon-times" aria-hidden="true"></i></a></td>
						</tr>
					</table>
				</div>
				<div class="center-btn">
					<input value="Add Attachment" class="theme-btn" type="submit">
					<button type="button" class="btn-cancel">Cancel</button>
				</div>
			</div>
            */ ?>
		</div>
	</div>

<div id="login_popup_open" class="sectionOverlay">
  <div class="overlayer"></div>
  <div class="screenTable">
    <div class="screenCell">
      <div class="sectioninner guest-user zoomIn">
        <a class="basic_close"><i class="fa fa-times" aria-hidden="true"></i></a>
       		
	        <div class="popup-content guest-user">
	        	<div class="login-form-container">
		        	<div class="modal-dialog">
					    <div class="modal-content login">
					        <form class="form-horizontal">
						        <div class="modal-header">
					                <a href="/"><img class="client-logo" src="/assets/<?= $assets_folder_path ?>/img/client-logo.png" alt="<?= __('Home') ?>" /></a>
					            </div>
					            <div class="modal-body">
					            	<fieldset>
					            		<h2>Log into your account </h2>
					            		<div class="form-group">
					                        <div class="col-sm-12">
					            				<a href="#" class="social-btn fb--btn"><i class="fa fa-facebook" aria-hidden="true"></i>Log in with Facebook</a>
					            			</div>
					            		</div>
					            		<div class="form-group">
					                        <div class="col-sm-12">		
					            				<a href="#" class="social-btn gplus--btn"><i class="fa fa-google-plus" aria-hidden="true"></i>Log in with Google</a>
					            			</div>
					            		</div>	
					                    <h2>Or Log In with your email</h2>
					                    <div class="form-group">
					                        <div class="col-sm-12">
					                            <div class="input-group login-input-group">
					                                <label class="input-group-addon" for="login-email">
					                                    <span class="sr-only">Email</span>
					                                    <span class="fa fa-envelope"></span>
					                                </label>
					                                <input class="form-control input-lg" placeholder="Email" value="" required="" type="text">
					                            </div>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        <div class="col-sm-12">
					                            <div class="input-group login-input-group">
					                                <label class="input-group-addon" for="login-password">
					                                    <span class="sr-only">Password</span>
					                                    <span class="fa fa-lock"></span>
					                                </label>
					                                <input required="" class="form-control input-lg" placeholder="Password" type="password">
					                                <label class="view-pwd" for="login-password">
					                                	<a href="#">
					                                		<i class="fa fa-eye" aria-hidden="true"></i>
					                                	</a>
					                                </label>
					                            </div>
					                        </div>
					                    </div>
					                    <div class="form-group login-buttons">
						                    <div>
						                        <input class="btn btn-lg btn-primary" id="login_button" name="login" value="Log in" type="submit">
						                    </div>
					                        <div>
					                            <label>
					                                <input name="remember" value="dont-remember" type="hidden"><!-- Default value for checkbox -->
					                                <label class="checkbox-styled">
					                                    <input id="optionsCheckbox" name="remember" value="remember" checked="" type="checkbox">
					                                    <span class="checkbox-icon"></span>
					                                </label>
					                                Keep me signed in for 1 day.
					                            </label>
					                        </div>
					                    </div>                            
										<div class="col-sm-12 text-center link-wrap">
											<p><a href="/admin/login/forgot_password/" id="passwordlink" class="usefull-link"><span>Forgot your password?</span></a></p>
											<p><a href="mailto:support@ideabubble.ie"  class="usefull-link">Can't log in?</a></p>
										</div>
									</fieldset>
								</div>
					            <div class="modal-footer">
					                <div class="text-center">
					                	<div class="layout-login-alternative_option clearfix">
					                        <p>Need an account? <span class="signup-text"><a href="#">Sign up</a></span></p>
					                    </div>
					                    <ul class="list-inline login-links">
					                    	<li><a href="/">Support</a></li>
					                    	<li><a href="/">Terms of use</a></li>
					                    	<li><a href="/">Privacy Policy</a></li>
					                    </ul>                    
					                    <div class="poweredby">
					                        <p>Powered by <a href="https://ideabubble.ie">Idea Bubble</a></p>
					                    </div>
					                </div>
					            </div>
							</form>
					    </div>
					</div>
				</div>		
	        </div>
      </div>
    </div>
  </div>
</div>

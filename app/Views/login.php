<div class="main-wrapper">
	<div class="content-body m-0 p-0">
		<div class="login-register-wrap">
			<div class="row">
				<div class="d-flex align-self-center justify-content-center order-2 order-lg-1 col-lg-4 col-12">
					<div class="login-register-form-wrap">
						<div class="content">
							<center>
								<img src="<?=BASE_URL_ASSETS_IMG?>logo-single.png" width="100px" />
							</center><br/>
							<h3><?=APP_NAME?></h3>
							<p>Please enter your username and password</p>
						</div>
						<div class="login-register-form">
							<form id="login-form" method="POST">
								<div class="row">
									<div class="col-12 mb-20">
										<div class="alert alert-dark d-none" role="alert" id="warning-element">
											<strong></strong>
											<span class="close"><i class="fa fa-close"></i></span>
										</div>
									</div>
									<div class="col-12 mb-20">
										<input id="username" name="username" class="form-control" type="text" placeholder="Username">
									</div>
									<div class="col-12 mb-20">
										<input id="password" name="password" class="form-control" type="password" placeholder="Password">
										<div class="show-password">
											<i class="fa fa-eye"></i>
										</div>
									</div>
									<div class="col-6 mb-20">
										<input id="captcha" name="captcha" class="form-control" type="text" placeholder="Captcha">
									</div>
									<div class="col-6 mb-20 pl-1">
										<img id="captchaImage" class="mx-auto" style="max-width: 150px; max-height: 46px;"/>
										<button type="button" class="button button-box pull-right" id="btnRefreshCaptcha"><i class="fa fa-refresh"></i></button>
									</div>
									<div class="col-12 mt-10">
										<button id="loginSubmitBtn" type="submit" class="button button-primary button-outline">Login</button>
										<a class="pull-right mt-20" id="clearCacheReloadLink" href="#">Clear Cache & Reload</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="login-register-bg order-1 order-lg-2 col-lg-8 col-12">
					<div class="content">
						<h1>Login</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?=BASE_URL_ASSETS_JS?>login.js?<?=date('YmdHis')?>"></script>
<?php

use App\Models\User;

$isLoggedIn = User::user();
if($isLoggedIn) {
    die("<script>location.href = '/dashboard';</script>");
}
?>

<div class="main-content login-panel login-panel-2">
	<h3 class="panel-title">Login</h3>
	<div class="login-body login-body-2">
		<div class="top d-flex justify-content-between align-items-center">
			<div class="logo">
				<img src="/assets/images/logo-black-new.png" class="img_logo" alt="Logo">
			</div>
			<a href="/" aria-label="go to Home"><i class="fa-duotone fa-house-chimney"></i></a>
		</div>
		<div class="bottom">
			<form method="post" id="form-signin">
				<input type="hidden" name="csrf_token" value="">
				<div class="input-group mb-25">
					<input required name="email" type="email" class="form-control" autocomplete="off" required placeholder="Email address">
					<span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
				</div>
				<div class="input-group mb-20">
					<input type="password" required name="password" class="form-control rounded-end" autocomplete="off" placeholder="Password">
					<a role="button" class="password-show"><i class="fa-duotone fa-eye"></i></a>
				</div>
				<div class="d-flex justify-content-between mb-25">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="remember" value="" id="loginCheckbox">
						<label class="form-check-label text-white" for="loginCheckbox">
							Remember Me
						</label>
					</div>
					<a href="forget" class="text-white fs-14">Forgot Password?</a>
				</div>
				<button type="submit" name="sumit_login" class="btn btn-primary w-100 login-btn">Login</button>
			</form>
			<div class="other-option">
				<p>Or continue with</p>
				<div class="social-box d-flex justify-content-center gap-20">
					<a href="/google_login" aria-label="login as google"><i class="fa-brands fa-google text-dark"></i></a>
				</div>
			</div>

			<div class="other-option">
				<p class="mb-0">Don't have an account? <a href="/signup" class="text-white text-decoration-underline">create</a></p>
			</div>
		</div>
	</div>

	<!-- footer start -->
	<?php require_once __DIR__ . "/footer.php"; ?>
	<!-- footer end -->
</div>

<script type="text/javascript">
	$("#form-signin").on("submit", function(e) {
		e.preventDefault();
		let formData = $(this).serialize(), 
			button = $(this).find('button[type="submit"]');

		button.addClass('loading');
		$.post("/ajax/auth/signin", formData, function(resp) {
			button.removeClass('loading');
			if(!resp.success) {
				Swal.fire(resp.alert);
				return false;
			}

			location.href = resp.data.redirect;
		}, 'json');
	});
</script>
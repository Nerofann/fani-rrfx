<?php
use App\Models\Country;
use App\Models\Helper;

$referral = Helper::form_input($_GET['referral'] ?? "");
?>

<div class="main-content login-panel">
	<div class="login-body">
		<div class="top d-flex justify-content-between align-items-center">
			<div class="logo">
				<img src="/assets/images/logo-rrfx3.png" alt="Logo">
			</div>
			<a href="/"><i class="fa-duotone fa-house-chimney"></i></a>
		</div>
		<div class="bottom">
			<h3 class="panel-title">Registration</h3>
			<form id="form-signup" method="post">
				<input type="hidden" name="csrf_token" value="">
				<div class="input-group mb-25">
					<span class="input-group-text"><i class="fa-regular fa-user"></i></span>
					<input type="text" name="fullname" required data-parsley-required class="form-control"
						autocomplete="off" placeholder="Full Name">
				</div>
				<div class="input-group mb-25">
					<span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
					<input type="email" required name="email" class="form-control" autocomplete="off"
						placeholder="Email">
				</div>
				<div class="input-group mb-20">
					<span class="input-group-text"><i class="fa-regular fa-lock"></i></span>
					<input type="password" required name="password" class="form-control rounded-end"
						autocomplete="off" placeholder="Password">
					<a role="button" class="password-show"><i class="fa-duotone fa-eye"></i></a>
				</div>
				<div class="input-group mb-25">
					<span class="input-group-text"><i class="fa-regular fa-at"></i></span>
					<input type="text" name="refferal" class="form-control" autocomplete="off" placeholder="Referal" value="<?= $referral ?>">
				</div>
				<hr>
				<div class="mb-25">
					<div class="input-group mb-0">
						<select name="phone_code" class="input-group-text" style="width: fit-content;">
							<?php foreach (Country::countries() as $country): ?>
								<?php if ($country['COUNTRY_PHONE_CODE'] == "+62"): ?>
									<option value="<?= $country['COUNTRY_PHONE_CODE'] ?>">
										<?= $country['COUNTRY_PHONE_CODE'] ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
						<input type="number" name="phone" required data-parsley-required class="form-control"
							min="0" autocomplete="off" placeholder="Phone Number">
					</div>
					<a href="javascript:void(0)" class="float-end" id="resendCode"><small class="text-sm text-white text-decoration-underline">Send Code</small></a>
				</div>
				<div class="input-group mb-25" style="margin-top: 2rem !important;">
					<span class="input-group-text"><i class="fa-regular fa-key"></i></span>
					<input type="number" name="otp" class="form-control" autocomplete="off" placeholder="OTP Code"
						value="">
				</div>
				<div class="d-flex justify-content-between mb-25">
					<div class="form-check">
						<input class="form-check-input" name="terms" type="checkbox" required checked
							id="loginCheckbox">
						<label class="form-check-label text-white" for="loginCheckbox">
							Saya telah membaca dan menyetujui <a href="#" data-bs-toggle="modal"
								data-bs-target="#addTaskModal" class="text-white text-decoration-underline">Syarat
								dan Ketentuan serta Kebijakan Privasi</a>
						</label>
					</div>
				</div>
				<button type="submit" name="submit_register" class="btn btn-primary w-100 login-btn">Sign up</button>
			</form>
			<!-- <div class="other-option">
				<p>Or continue with</p>
				<div class="social-box d-flex justify-content-center gap-20">
					<a href="#"><i class="fa-brands fa-facebook-f"></i></a>
					<a href="#"><i class="fa-brands fa-twitter"></i></a>
					<a href="#"><i class="fa-brands fa-google"></i></a>
					<a href="#"><i class="fa-brands fa-instagram"></i></a>
				</div>
			</div> -->
		</div>
	</div>

	<!-- footer start -->
	<?php require_once __DIR__ . "/footer.php"; ?>
	<!-- footer end -->
</div>

<script type="text/javascript">
	let resendText = $('#resendCode').find('small')
	$(document).ready(function() {
		$('#resendCode').on('click', function () {
			if (!resendText.hasClass('text-muted')) {
				Swal.fire({
					title: `Kirim Kode OTP`,
					text: "Mohon untuk memastikan bahwa nomer ini benar-benar milik anda",
					icon: "question",
					showCancelButton: true,
					reverseButtons: true,
				}).then((result) => {
					if(result.isConfirmed) {
						Swal.fire({
							text: "Mohon tunggu...",
							allowOutsideClick: false,
							didOpen: function() {
								Swal.showLoading();
							}
						})
						
						$.post("/ajax/auth/sendOtp", $('#form-signup').serialize(), function (resp) {
							Swal.fire(resp.alert).then(() => {
								if (resp.success) {
									startCount(resp.data.delay);
								}
							});
						}, 'json')
					}
				})
			}
		})
		
		$("#form-signup").on("submit", function(e) {
			e.preventDefault();
			let formData = $(this).serialize(),
				button = $(this).find('button[type="submit"]');
			
			button.addClass('loading');
			$.post("/ajax/auth/signup", formData, function(resp) {
				button.removeClass('loading');
				Swal.fire(resp.alert).then(() => {
					if(resp.success) {
						location.href = resp.data.redirect;
					}
				});
			}, 'json');
		});
	})

	function startCount(second) {
		let intervall = setInterval(function () {
			resendText.text(`Send Code: ${second}s`);
			if (!resendText.hasClass('text-muted')) {
				resendText.addClass('text-muted');
			}

			second--;
			if (second <= 0) {
				clearInterval(intervall);
				resendText.text('Send Code').removeClass('text-muted').addClass('text-white');
			}
		}, 1000)
	}
</script>
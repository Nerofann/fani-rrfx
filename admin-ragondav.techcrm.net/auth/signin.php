<div class="row signpages text-center">
    <div class="col-md-12">
        <div class="card">
            <div class="row row-sm">
                <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                    <div class="mt-5 pt-4 p-2 pos-absolute">
                        <img src="/assets/img/brand/logo-light.png" class="header-brand-img mb-4" alt="logo">
                        <div class="clearfix"></div>
                        <img src="/assets/img/svgs/user.svg" class="ht-100 mb-0" alt="user">
                        <h5 class="mt-4 text-white">Admin Area</h5>
                        <span class="tx-white-6 tx-13 mb-5 mt-xl-0">discover and connect with the global community</span>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form ">
                    <div class="main-container container-fluid">
                        <div class="row row-sm">
                            <div class="card-body mt-2 mb-2">
                                <img src="/assets/img/brand/logo-light.png" class="d-lg-none header-brand-img text-start float-start mb-4 error-logo-light" alt="logo">
                                <img src="/assets/img/brand/logo.png" class=" d-lg-none header-brand-img text-start float-start mb-4 error-logo" alt="logo">
                                <div class="clearfix"></div>
                                <form action="" method="post" id="form-login">
                                    <h5 class="text-start mb-2">Signin to Your Account</h5>
                                    <p class="mb-4 text-muted tx-13 ms-0 text-start">Signin to create, discover and connect with the global community</p>
                                    <div class="form-group text-start">
                                        <label>Username</label>
                                        <input class="form-control" name="username" placeholder="Enter your username" type="text" autocomplete="off" required>
                                    </div>
                                    <div class="form-group text-start">
                                        <label>Password</label>
                                        <input class="form-control" name="password" placeholder="Enter your password" type="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-main-primary btn-block text-white">Sign In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-login').on('submit', function(event) {
            event.preventDefault();

            let button = $(this).find('button[type="submit"]');
            let data = Object.fromEntries(new FormData(this).entries());
            button.addClass('loading');
            $.post("/ajax/auth/signin", data, function(resp) {
                button.removeClass('loading');
                if(!resp.success) {
                    Swal.fire(resp.alert);
                    return false;
                }

                location.href = resp.data.redirect;
            }, 'json')
        })
    })
</script>
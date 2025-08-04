<div class="dashboard-breadcrumb mb-25">
    <h2>Security</h2>
</div>

<div class="row">
    <div class="col-6">
        <div class="panel">
            <div class="card">
                <form action="" method="post" id="form-update-password">
                    <div class="card-header">
                        Password
                    </div>
                    <div class="card-body">
                        <div class="mt-3">
                            <label for="basicInput" class="form-label">Current Password</label>
                            <input type="password" name="current_pass" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="mt-3">
                            <label for="basicInput" class="form-label">New Password</label>
                            <input type="password" name="new_pass" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="mt-3">
                            <label for="basicInput" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_new_pass" class="form-control" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" name="change_pass">Submit</button>
                        <button type="reset" class="btn btn-danger" name="reset">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="panel">
            <div class="card">
                <div class="card-header">
                    2FA Auth (Coming Soon)
                </div>
                <div class="card-body">
                    <div class="mt-3">
                        <label for="basicInput" class="form-label">Key</label>
                        <input type="password" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="mt-3">
                        <label for="basicInput" class="form-label">Code</label>
                        <input type="password" class="form-control" autocomplete="off" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    <button type="reset" class="btn btn-danger" name="reset">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-update-password').on('submit', function(event) {
            event.preventDefault();
            let data = $(this).serialize();
            let button = $(this).find('button[type="submit"]');

            button.addClass('loading');
            $.post("/ajax/post/profile/update-password", data, (resp) => {
                button.removeClass('loading');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                })
            }, 'json')
        })
    })
</script>
<?php
    use App\Models\Helper;
    $SFD  = Helper::getSafeInput($_GET);
    $usrx = $SFD["d"];
    $SQL_USER = mysqli_query($db, '
        SELECT
            *
        FROM tb_member
        WHERE MD5(MD5(tb_member.ID_MBR)) = "'.$usrx.'"
    ');
    if((!$SQL_USER) || mysqli_num_rows($SQL_USER) == 0){
        die("<script>alert('User not found!');location.href='/member/user/view'</script>");
    }
    $RSLT_USER = mysqli_fetch_assoc($SQL_USER);
?>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">User Edit</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Detail member</h4>
            </div>
            <div class="card-body">
                <form method="post" id="member-form">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="fullname" class="form-label">Fullname</label>
                                <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Fullname"  value="<?php echo $RSLT_USER['MBR_NAME'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone Number"  value="<?php echo $RSLT_USER['MBR_PHONE'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user" class="form-label">Username</label>
                                <input type="text" class="form-control" id="user" placeholder="Username"  value="<?php echo $RSLT_USER['MBR_USER'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label for="email" class="form-label">Email</label>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalChangeEmail" class="text-decoration-underline">Change Email</a>
                                </div>
                                <input type="text" class="form-control" id="email" placeholder="Email"  value="<?php echo $RSLT_USER['MBR_EMAIL'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country"  value="<?php echo $RSLT_USER['MBR_COUNTRY'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="Address"  value="<?php echo $RSLT_USER['MBR_ADDRESS'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="address" class="form-label">City</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="City"  value="<?php echo $RSLT_USER['MBR_CITY'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="zip" class="form-label">Zip</label>
                                <input type="number" class="form-control" name="zip" id="zip" placeholder="Zip"  value="<?php echo $RSLT_USER['MBR_ZIP'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input type="hidden" name="mbrx" value="<?= $usrx ?>">
                            <button type="submit" name="submit_detail_member" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Log Change email</h5>
            </div>
            <div class="card-body">
                <div class="table-reponsive">
                    <table class="table table-hover table-striped table-bordered" id="table-log-email">
                        <thead>
                            <tr class="text-center">
                                <th>Datetime</th>
                                <th>Email Lama</th>
                                <th>Email Baru</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modalChangeEmail" aria-labelledby="label-modalChangeEmail">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" id="email-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email_sekarang" class="form-label">Email Sekarang</label>
                                <input type="email" name="email_sekarang" id="email_sekarang" class="form-control" placeholder="Email Sekarang" value="<?php echo $RSLT_USER['MBR_EMAIL'] ?>" readonly>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email_baru" class="form-label">Email Baru</label>
                                <input type="email" name="email_baru" id="email_baru" class="form-control" placeholder="Email Baru" required>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="bukti_dokument" class="form-label">Bukti Dokument</label>
                                <input type="file" name="bukti_dokument" id="bukti_dokument" class="dropify form-control" data-max-file-size="5M" data-allowed-file-extensions="jpg png" required>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="modal-footer">
                    <div class="float-end">
                        <input type="hidden" name="mbrx" value="<?= $usrx ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit_change_email" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        $('#table-log-email').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/member/email",
                contentType: "application/json",
                data: {usrx : '<?= md5(md5($RSLT_USER["MBR_ID"])) ?>'},
                type: "GET"
            },
        });

        $('#member-form').on('submit', function(ev){
            ev.preventDefault();
            let data = Object.fromEntries(new FormData(this));
            $.post("/ajax/post/member/user", data, function(resp) {
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });
            }, 'json')
        });

        $('#email-form').on('submit', function(ev){
            ev.preventDefault();
            let data = new FormData(this);
            $.ajax({
                url         : '/ajax/post/member/email',
                type        : 'POST',
                dataType    : 'JSON',
                enctype     : 'multipart/form-data',
                data        : data,
                contentType : false,
                chache      : false,
                processData : false
            }).done((resp) => {
                $('#modalChangeEmail').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });

            });
        });
    });
</script>
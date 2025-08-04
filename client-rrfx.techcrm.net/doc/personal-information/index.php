<?php

function is_disable($inp) {
    if(empty($inp)) return false;
    if($inp == "-") return false;
    if(strlen($inp) <= 1) return false;
    
    return "disabled";
}
?>

<div class="dashboard-breadcrumb mb-25">
    <h2>Personal Information</h2>
</div>
<div class="panel">
    <div class="panel-body">
        <form action="" method="post" id="form-personal-information">
            <div class="public-information mb-25">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="admin-profile">
                            <div class="d-flex justify-content-center">
                                <div class="custom-avatar-container">
                                    <img class="custom-avatar" src="<?= App\Models\User::avatar($user['MBR_AVATAR']) ?>" alt="admin">
                                    <label for="avatar" class="edit-icon"><i class="fas fa-camera"></i></label>
                                </div>
                                <input type="file" name="avatar" id="avatar" class="d-none">
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('#avatar').on('change', function() {
                                    const file = this.files
                                    if(file.length) {
                                        let fileReader = new FileReader()
                                        fileReader.onload = (event) => {
                                            $('img[alt="admin"]').attr('src', event.target.result)
                                            // $('button[name="update-avatar"]').removeClass('d-none')
                                        }
            
                                        fileReader.readAsDataURL(file[0])
                                    }
                                })
                            })
                        </script>
            
                    </div>
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label">Full Name</label>
                                <input disabled type="text" class="form-control" value="<?= $user['MBR_NAME'] ?>">
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label">Email</label>
                                <input disabled type="email" class="form-control" value="<?= $user['MBR_EMAIL'] ?>">
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label">No. Telepon</label>
                                <input disabled type="text" class="form-control" value="<?= $user['MBR_PHONE'] ?>">
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label">Negara</label>
                                <input type="text" class="form-control" value="<?= $user['MBR_COUNTRY'] ?>" disabled>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label required">Kota</label>
                                <input name="city" type="text" class="form-control" value="<?= $user['MBR_CITY'] ?>" placeholder="Kota" required>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="basicInput" class="form-label required">Kode Pos</label>
                                <input name="zip" type="number" class="form-control" value="<?= $user['MBR_ZIP'] ?>" required>
                            </div>
                            <div class="col-sm-5 mb-3">
                                <label for="tempat_lahir" class="form-label required">Place of birth</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="<?= $user['MBR_TMPTLAHIR'] ?>" placeholder="place of birth" required>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="tanggal_lahir" class="form-label required">Date of birth</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" max="<?php echo date("Y-01-01", strtotime("-18 year")); ?>" class="form-control" value="<?= $user['MBR_TGLLAHIR'] ?>" required>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Laki-laki" <?= ($user['MBR_JENIS_KELAMIN'] == "Laki-laki")? "selected" : ""; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($user['MBR_JENIS_KELAMIN'] == "Perempuan")? "selected" : ""; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="basicInput" class="form-label">Address</label>
                                <textarea name="address" class="form-control h-150-p" placeholder="Address"><?= $user['MBR_ADDRESS'] ?></textarea>
                            </div>
        
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" name="save_personal_info">Submit</button>
                                <button type="reset" class="btn btn-danger" name="reset">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#form-personal-information").on('submit', function(event) {
            event.preventDefault();
            let data = new FormData(this);
            let button = $(this).find('button[type="submit"]');

            button.addClass('loading');
            $.ajax({
                url: "/ajax/post/profile/personal-information",
                type: "post",
                dataType: "json",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
            }).done((resp) => {
                button.removeClass('loading');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                })
            })
        })
    })
</script>
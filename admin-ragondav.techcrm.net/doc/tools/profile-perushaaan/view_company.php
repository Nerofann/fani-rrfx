<?php if($permisCreate = $adminPermissionCore->isHavePermission($moduleId, "view_company")){ ?>
    
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12 col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Company</div>
                    <hr>
                    test Company
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" onclick="window.location.href='/doc/tools/profile-perushaaan/view_company.php'">View Company</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
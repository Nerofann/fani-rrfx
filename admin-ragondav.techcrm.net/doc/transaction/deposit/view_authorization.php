
<div class="card custom-card overflow-hidden">
    <div class="card-header">
        <h5 class="main-content-label mb-1">Authorization</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" width="100%" id="table-authorization">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">Date</th>
                        <th style="vertical-align: middle" class="text-center">Name</th>
                        <th style="vertical-align: middle" class="text-center">Email</th>
                        <th style="vertical-align: middle" class="text-center">Phone</th>
                        <th style="vertical-align: middle" class="text-center">Login</th>
                        <th style="vertical-align: middle" class="text-center">Amount</th>
                        <th style="vertical-align: middle" class="text-center">Pic</th>
                        <th style="vertical-align: middle" class="text-center" width="1%">#</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="myModalAuth" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="authorization-form">
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-borderless">
                            <tr>
                                <td>Login</td>
                                <td><input type="text" id="auth-login" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td><input type="text" id="auth-name" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input type="text" id="auth-email" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td><input type="text" id="auth-amnt" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Bank Source</td>
                                <td><input type="text" id="auth-bksrc" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Bank Destination</td>
                                <td><input type="text" id="auth-bkdst" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;">Voucher</td>
                                <td>
                                    <input type="text" name="voucher" value="-" autocomplete="off" class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle;">Note</td>
                                <td>
                                    <input type="text" class="form-control" autocomplete="off" name="note" value="-" required>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="auth-dpx" id="auth-dpx" value="" readonly required>
                    <input type="hidden" name="auth-act" id="auth-act" value="" readonly required>
                    <button type="submit" id="sbmtauth" style="display: none;"></button>
                    <button type="button" value="accept" class="btn btn-success act-btnauth">Accept</button>
                    <button type="button" value="reject" class="btn btn-danger act-btnauth">Reject</button>
                </div>
            </form>
        </div>
    
    </div>
</div>
<script>
    $(document).ready(() => {
        $('.act-btnauth').on('click', function(e){
            $('#auth-act').val($(this).val());
            $('#sbmtauth').click();
        });
        $('#authorization-form').on('submit', function(e){
            e.preventDefault();
            let data = Object.fromEntries(new FormData(this));
            $.post("/ajax/post/transaction/authorization", data, function(resp) {
                $('#myModalAuth').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });
            }, 'json');
        });
        $('#table-authorization').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/transaction/authorization",
                contentType: "application/json",
                type: "GET"
            },
            drawCallback : () => {
                $('.edt-btn').on('click', function(){
                    for(var [key, value] of Object.entries(JSON.parse(atob($(this).data('jsn'))))) {
                        if($(`#${key}`)[0]?.tagName == 'INPUT'){
                            if($(`#${key}`).attr('type') != 'file'){
                                if($(`#${key}`).attr('type') == 'checkbox'){
                                    if((($(`#${key}`).prop('checked')) && $(`#${key}`).val() == value) || ((!$(`#${key}`).prop('checked')) && $(`#${key}`).val() != value)){
                                        $(`#${key}`)[0].click();
                                    }
                                }else if($(`#${key}`).attr('class')?.includes('frmtRph')){
                                    // console.log(value, $(`#${key}`));
                                    $(`#${key}`).val(formatRupiah(value.toString()));
                                }else{ 
                                    if($(`#${key}`).attr('type') != 'checkbox'){
                                        // console.log(key);
                                        $(`#${key}`).val(value); 
                                    }
                                }
                            }else{
                                fln = (value !== null) ? `<?= $aws_folder ?>${value}` : dfltflnm;
                                $(`.dropify-render`).children().attr('src', fln);
                                $(`.dropify-filename-inner`).html(value);
                            }
                        }else if($(`#${key}`)[0]?.tagName == 'SELECT' || $(`#${key}`)[0]?.tagName == 'BUTTON'){
                            $(`#${key}`).val(value);
                            // if($(`#${key}`).attr('id') == 'edt_head'){
                            //     $(`#${key}`)[0].dispatchEvent(new Event('change'));
                            //     console.log('dispatched');
                            // }
                            // dsptch();
                        }else if($(`#${key}`)[0]?.tagName == 'TEXTAREA'){
                            $(`#${key}`).html(value.replaceArray(["\\\\r\\\\n", "\\\\n", "&amp;nbsp;", "\\\\"], ["&#13;&#10;", "&#13;", " ", ""]));
                        }
                    }
                });
            }
        });
    });
</script>
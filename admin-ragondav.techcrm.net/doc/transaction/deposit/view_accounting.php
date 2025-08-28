
<div class="card custom-card overflow-hidden">
    <div class="card-header">
        <h5 class="main-content-label mb-1">Accounting</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" width="100%" id="table-accounting">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">Date</th>
                        <th style="vertical-align: middle" class="text-center">Name</th>
                        <th style="vertical-align: middle" class="text-center">Email</th>
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
<div id="myModalAcc" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="accounting-form">
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-borderless">
                            <tr>
                                <td>Login</td>
                                <td><input type="text" id="acc-login" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td><input type="text" id="acc-name" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input type="text" id="acc-email" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr class="extr-elem">
                                <td>Amount</td>
                                <td><input type="text" id="acc-amntl" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr class="extr-elem">
                                <td>Rate</td>
                                <td><input type="text" id="acc-rate" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td><input type="text" id="acc-amnt" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Bank Source</td>
                                <td><input type="text" id="acc-bksrc" class="form-control text-dark" readonly></td>
                            </tr>
                            <tr>
                                <td>Bank Destination</td>
                                <td><input type="text" id="acc-bkdst" class="form-control text-dark" readonly></td>
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
                    <input type="hidden" name="acc-dpx" id="acc-dpx" value="" readonly required>
                    <input type="hidden" name="acc-act" id="acc-act" value="" readonly required>
                    <button type="submit" id="sbmtacc" style="display: none;"></button>
                    <button type="button" value="accept" class="btn btn-success act-btna">Accept</button>
                    <button type="button" value="reject" class="btn btn-danger act-btna">Reject</button>
                </div>
            </form>
        </div>
    
    </div>
</div>
<script>
    $(document).ready(() => {
        $('.act-btna').on('click', function(e){
            $('#acc-act').val($(this).val());
            $('#sbmtacc').click();
        });
        $('#accounting-form').on('submit', function(e){
            e.preventDefault();
            let data = Object.fromEntries(new FormData(this));
            $.post("/ajax/post/transaction/accounting", data, function(resp) {
                $('#myModalAcc').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });
            }, 'json');
        });
        $('#table-accounting').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
			buttons: [
				{
					extend: 'excel',
					text: 'Excel',
				},
				{
					extend: 'copy',
					text: 'Copy'
				}
			],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/transaction/accounting",
                contentType: "application/json",
                type: "GET"
            },
            drawCallback : () => {
                $('.edt-btn').on('click', function(){
                    for(var [key, value] of Object.entries(JSON.parse(atob($(this).data('jsn'))))) {
                        if(key == 'acc-rate'){
                            $('.extr-elem').css('display', ((value == 0) ? 'none' : ''));
                        }
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
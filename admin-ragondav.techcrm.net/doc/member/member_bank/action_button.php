<?php if($permisAccept = $adminPermissionCore->isHavePermission($moduleId, "action")) : ?>
    <script type="text/javascript">
        $(document).ready(function(){
            if(table_pending) {
                table_pending.on('draw.dt', function() {
                    $.each($('#table-pending tbody tr'), (i, tr) => {
                        let td = $(tr).find('td').eq(4);
                        let actionArea = $(td).find('.action');
                        if(td && actionArea) {
                            let id = actionArea.data('id');
                            if(!actionArea.find('.btn-accept').length) {
                                actionArea.append(`<a class="btn btn-sm btn-success btn-accept" data-id="${id}" data-type="accept"><i class="fas fa-check text-white"></i></a>`);
                            }

                            if(!actionArea.find('.btn-reject').length) {
                                actionArea.append(`<a class="btn btn-sm btn-danger btn-reject" data-id="${id}" data-type="reject"><i class="fas fa-close text-white"></i></a>`);
                            }
                        }
                    })

                    $('.btn-accept, .btn-reject').on('click', function(evt) {
                        let target = $(evt.currentTarget);
                        let action = target.data('type');
                        if(target) {
                            Swal.fire({
                                title: `${action.charAt(0).toUpperCase() + action.slice(1)} Member Bank`,
                                text: `Konfirmasi untuk melanjutkan`,
                                icon: "question",
                                showCancelButton: true,
                                reverseButtons: true
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    Swal.fire({
                                        text: "Please wait...",
                                        allowOutsideClick: false,
                                        didOpen: function() {
                                            Swal.showLoading();
                                        } 
                                    })

                                    let data = target.data();
                                    $.post("/ajax/post<?= $permisAccept['link'] ?>", data, (resp) => {
                                        Swal.fire(resp.alert).then(() => {
                                            if(resp.success) {
                                                table_pending.draw();
                                                if(table_history) {
                                                    table_history.draw();
                                                }
                                            }
                                        })
                                    }, 'json')
                                }
                            })

                        }
                    })
                })
            }
        })
    </script>
<?php endif; ?>
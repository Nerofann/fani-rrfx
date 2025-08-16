<?php
$dt->query("
    SELECT 
        tr.ACC_DATETIME,
        tr.ACC_LOGIN, 
        trc.RTYPE_TYPE,
        trc.RTYPE_CURR,
        trc.RTYPE_RATE,
        tr.ACC_WPCHECK,
        MAX(tb_note.ID_NOTE) as ID_NOTE,
        tb_note.NOTE_NOTE,
        tr.ACC_LAST_STEP,
        tr.ID_ACC,
        tr.ACC_STS
    FROM tb_racc tr
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    LEFT JOIN tb_note  ON (tb_note.NOTE_RACC = tr.ID_ACC AND tb_note.NOTE_MBR = tr.ACC_MBR)
    WHERE ACC_MBR = ".$user['MBR_ID']."
    GROUP BY tr.ID_ACC
");

$dt->hide("ACC_STS");
$dt->hide("ACC_LAST_STEP");
$dt->hide("ID_NOTE");
$dt->edit("ACC_DATETIME", function($data) {
    return '<div class="text-center">'.(date("Y-m-d H:i:s", strtotime($data['ACC_DATETIME']))).'</div>';
});

$dt->edit("ACC_WPCHECK", function($data) {
    if(strtoupper($data['RTYPE_TYPE']) == "DEMO") {
        return "-";
    
    }elseif($data['ACC_WPCHECK'] == 0 && $data['ACC_STS'] == 0) {
        return '<a href="/account/create?page='.$data['ACC_LAST_STEP'].'"><span class="badge bg-warning small">Lanjutkan</span></a>';
    
    }elseif($data['ACC_WPCHECK'] == 0 && $data['ACC_STS'] == 1) {
        return '<a href="/account/create?page='.$data['ACC_LAST_STEP'].'"><span class="badge bg-primary small">Menunggu Konfirmasi WPB</span></a>';
    
    }elseif($data['ACC_WPCHECK'] == 0 && $data['ACC_STS'] == 2) {
        return '<a href="/account/create?page='.$data['ACC_LAST_STEP'].'"><span class="badge bg-danger small">Ditolak</span></a>';
    
    }elseif(in_array($data['ACC_WPCHECK'], [1, 2]) && $data['ACC_STS'] == 1) {
        return '<a href="/account/create?page='.$data['ACC_LAST_STEP'].'"><span class="badge bg-info small">Deposit New Account</span></a>';

    }elseif($data['ACC_WPCHECK'] == 6 && $data['ACC_STS'] == -1 && $data['ACC_LOGIN'] != 0) {
        return '<a href="javascript:void(0)"><span class="badge bg-success small">Aktif</span></a>';
        
    }else {
        return '<a href="javascript:void(0)"><span class="badge bg-secondary small">Diprosess</span></a>';
        
    }
});

$dt->edit("ID_ACC", function($data) {
    if(strtoupper($data['RTYPE_TYPE']) != "DEMO") {
        $documents = ($data['ACC_WPCHECK'] == 6)
            ?  '<a href="/document?id='.(md5(md5($data['ID_ACC'] ?? "-"))).'" title="Documents"><button class="btn btn-info"><i class="fa-light fa-file"></i></button></a>'
            : '';

        return '
            <div class="text-center">
                <a href="/deposit" title="Deposit"><button class="btn btn-success"><i class="fa-light fa-arrow-right-to-bracket"></i></button></a>
                <a href="/withdrawal" title="Withdrawal"><button class="btn btn-danger"><i class="fa-light fa-arrow-right-from-bracket"></i></button></a>
                '.$documents.'
            </div>
        ';
    }
});

echo $dt->generate()->toJson();
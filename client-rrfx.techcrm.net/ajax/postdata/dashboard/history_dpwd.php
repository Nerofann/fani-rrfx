<?php
$rangeDate  = [];
$result     = [
    'DP_IDR' => [],
    'DP_USD' => [],
    'WD_IDR' => [],
    'WD_USD' => []
];

$dateFrom = date_create(date("Y-m-d", strtotime("-7 days")));
$dateTo   = date_create(date("Y-m-d"));
$dateDiff = date_diff($dateFrom, $dateTo);

/** Get Range Date */
for($i = 0; $i < $dateDiff->d; $i++) {
    // Fill Default Value
    $result['DP_IDR'][$i] = 0;
    $result['DP_USD'][$i] = 0;
    $result['WD_IDR'][$i] = 0;
    $result['WD_USD'][$i] = 0;
    

    $index_name = date("Y-m-d", strtotime("-{$i} day"));
    if(!in_array($index_name, $rangeDate)) {
        array_push($rangeDate, $index_name);
    }
}

$sql_get_dpwd = mysqli_query($db, "
    SELECT 
        IFNULL(SUM(tb_dpwd.DPWD_AMOUNT), 0) as AMOUNT,
        tb_dpwd.DPWD_TYPE,
        tr.ACC_LOGIN,
        trc.RTYPE_CURR,
        DATE(tb_dpwd.DPWD_DATETIME) as `DATE`
    FROM tb_dpwd
    JOIN tb_racc tr ON (tr.ACC_MBR = tb_dpwd.DPWD_MBR)
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    WHERE MD5(MD5(tb_dpwd.DPWD_MBR)) = '{$userid}'
    AND DATE(tb_dpwd.DPWD_DATETIME) BETWEEN '".date("Y-m-d", strtotime($dateFrom->format("Y-m-d")))."' AND '".date("Y-m-d", strtotime($dateTo->format("Y-m-d")))."'
    AND (
        (tb_dpwd.DPWD_TYPE = 1 AND tb_dpwd.DPWD_STS = -1)
        OR 
        (tb_dpwd.DPWD_TYPE = 2 AND tb_dpwd.DPWD_STS != 1)
    )
    GROUP BY 
        tb_dpwd.DPWD_TYPE, 
        tr.ACC_LOGIN, 
        trc.RTYPE_CURR,
        DATE(tb_dpwd.DPWD_DATETIME)
");

if($sql_get_dpwd && mysqli_num_rows($sql_get_dpwd) != 0) {
    while($row = mysqli_fetch_assoc($sql_get_dpwd)) {
        $amount     = floatVal($row['AMOUNT']);
        $getIndex   = array_search($row['DATE'], $rangeDate);

        if($getIndex !== FALSE) {
            switch(true) {
                case ($row['DPWD_TYPE'] == 1 && $row['RTYPE_CURR'] == "IDR"): 
                    $result['DP_IDR'][ $getIndex ] = $amount;
                    break;

                case ($row['DPWD_TYPE'] == 1 && $row['RTYPE_CURR'] == "USD"): 
                    $result['DP_USD'][ $getIndex ] = $amount;
                    break;

                case ($row['DPWD_TYPE'] == 2 && $row['RTYPE_CURR'] == "IDR"): 
                    $result['WD_IDR'][ $getIndex ] = $amount;
                    break;

                case ($row['DPWD_TYPE'] == 2 && $row['RTYPE_CURR'] == "USD"): 
                    $result['WD_USD'][ $getIndex ] = $amount;
                    break;
            }
        }
    }
}

JsonResponse([
    'success' => true,
    'message' => "Success",
    'data' => [
        'chart' => $result
    ]
]);
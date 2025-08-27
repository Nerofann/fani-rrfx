<?php
    $getData = $helperClass->getSafeInput($_GET);
    $currency = $getData['currency'] ?? "";
    $result = mysqli_query($db,"SELECT * FROM tb_bankadm");
    $data = [];
    if($result->num_rows < 1){
        ApiResponse([
            'status' => false,
            'message' => 'Data Bank Admin tidak tersedia',
            'response' => array() 
        ], 400);
    }else{
        while($row = $result->fetch_assoc()) {
            if(!empty($currency)) {
                if(strtoupper($currency) != $row['BKADM_CURR']) {
                    continue;
                }
            }
            $data[] = array(
                "id"            => md5(md5($row["ID_BKADM"])),
                "currency"      => $row["BKADM_CURR"],
                "bank_name"     => $row["BKADM_NAME"],
                "bank_holder"   => $row["BKADM_HOLDER"],
                "bank_account"  => $row["BKADM_ACCOUNT"],
            );
            
        }
        ApiResponse([
            'status' => true,
            'message' => 'Data Bank Admin tersedia',
            'response' => $data
        ], 200);
    }
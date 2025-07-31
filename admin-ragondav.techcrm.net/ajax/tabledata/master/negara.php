<?php
$currentLevel = $user['ADM_LEVEL'] ?? 0;
$dt->query("
    SELECT 
        COUNTRY_CODE,
        COUNTRY_NAME,
        COUNTRY_CURR,
        COUNTRY_PHONE_CODE,
        MD5(MD5(ID_COUNTRY)) as ID
    FROM tb_country
");

$dt->edit("ID", function($col) {
    $editButton = '<a href="javascript:void(0)" data-id="'.$col['ID'].'" data-name="'.$col['COUNTRY_NAME'].'" data-curr="'.$col['COUNTRY_CURR'].'" data-code="'.$col['COUNTRY_CODE'].'" data-phone="'.$col['COUNTRY_PHONE_CODE'].'" class="btn-edit btn btn-sm btn-success"><i class="fas fa-edit"></i></a>';
    $deleteButton = '<a href="javascript:void(0)" data-id="'.$col['ID'].'" class="btn-delete btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>';
    
    return '
        <div class="text-center">
            '.($editButton).'        
            '.($deleteButton).'        
        </div>
    ';
});

echo $dt->generate()->toJson(); // same as 'echo $dt->generate()';
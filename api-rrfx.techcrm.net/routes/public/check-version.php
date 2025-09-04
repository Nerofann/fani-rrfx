<?php
    $versionApp = 1.0;

    try {
        $version = App\Models\Helper::form_input($_POST['version'] ?? "1.0");
        if($version != $versionApp){
            ApiResponse([
                'status' => false,
                'message' => "Versi aplikasi tidak valid, mohon untuk unduh aplikasi terbaru di Play Store.",
                'response' => []
            ], 400);
        }
        
        ApiResponse([
            'status' => true,
            'message' => "Versi aplikasi sama",
            'response' => []
        ]);
       
    } catch (Exception $e){
        ApiResponse([
            'status' => false,
            'message' => $e->getMessage(),
            'response' => []
        ], 400);
    }
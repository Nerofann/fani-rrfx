<?php
global $ApiMeta;

$symbols = $ApiMeta->symbols([]);
if(!$symbols->success) {
    ApiResponse([
        'status' => false,
        'message' => $symbols->error,
        'response' => []
    ], 400);
}

$result = [];
foreach($symbols->message as $symbol) {
    $result[] = [
        'symbol' => $symbol->symbol,
        'contract_size' => $symbol->contractsize,
        'spread' => $symbol->spread,
        'digits' => $symbol->digits,
        'pricesettle' => $symbol->pricesettle,
        'trademode' => $symbol->trademode,
        'margininitial' => $symbol->margininitial,
        'volume_min' => $symbol->volumeMin,
        'volume_max' => $symbol->volumeMax,
    ];
}

ApiResponse([
    'status' => true,
    'message' => 'Symbols',
    'response' => $result
], 200);
<?php
    $dt->query('
        SELECT
            t.OPEN_TIME as open_time,
            t.LOGIN as login,
            t.TICKET as ticket,
            t.SYMBOL as symbol,
            ROUND(t.OPEN_PRICE, t.DIGITS) AS open_price,
            ROUND(t.VOLUME, 2) AS volume,
            t.SL AS sl,
            t.TP AS tp,
            t.CLOSE_TIME as close_time,
            t.CLOSE_PRICE AS close_price,
            t.COMMISION AS commission,
            t.SWAP AS swap,
            t.PROFIT AS profit
        FROM mt5_trades t
        WHERE t.CLOSE_TIME IS NOT NULL
    ');

    echo $dt->generate()->toJson();
?>
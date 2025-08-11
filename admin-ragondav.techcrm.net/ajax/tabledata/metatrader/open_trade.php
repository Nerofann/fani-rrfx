<?php
    $dt->query('
        SELECT
            t.OPEN_TIME AS datetime,
            t.LOGIN as login,
            t.TICKET as ticket,
            t.SYMBOL as symbol,
            ROUND(t.VOLUME, 2) AS volume,
            t.SL AS sl,
            t.TP AS tp,
            ROUND(t.OPEN_PRICE, t.DIGITS) AS price
        FROM mt5_trades t
        WHERE t.CLOSE_TIME IS NULL
    ');

    echo $dt->generate()->toJson();
?>
<?php
$dt->query("
    SELECT
        tb_symbolcat.SYMCAT_NAME AS KATEGORI,
        tb_symbol.SYM_NAME AS SYMBOL
    FROM tb_symbolcat
    JOIN tb_symbol ON(tb_symbol.ID_SYMCAT = tb_symbolcat.ID_SYMCAT)
");

echo $dt->generate()->toJson();
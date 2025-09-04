<?php
$dt->query("
    SELECT
        SYMCAT_NAME AS SYMBOL
    FROM tb_symbolcat
");

echo $dt->generate()->toJson();
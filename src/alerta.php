<?php

function pegar_alerta()
{
    if(!isset($_SESSION["alerta"])) return false;
    $alerta = $_SESSION["alerta"];
    $_SESSION["alerta"] = null;
    return $alerta;
}

function novo_alerta($tipo, $mensagem)
{
    $_SESSION["alerta"] = [
        "tipo" => $tipo,
        "mensagem" => $mensagem
    ];
}

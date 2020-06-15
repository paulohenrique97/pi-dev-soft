<?php

require_once "db.php";

function logado()
{
    if (!isset($_SESSION["logado"])) return false;
    return (bool) $_SESSION["logado"];
}

function administrador()
{
    if (!isset($_SESSION["usuario"]["admin"])) return false;
    return (bool) $_SESSION["usuario"]["admin"];
}

function logout()
{
    session_destroy();
    $_SESSION["usuario"] = [
        "admin" => false
    ];
    $_SESSION["logado"] = false;
}

function atualiza_usuario()
{
    if (!isset($_SESSION["usuario"])) return;
    $id = $_SESSION["usuario"]["id"];
    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "SELECT * FROM $tabela_usuarios WHERE id = $id";
    $usuario = select($sql)[0];
    if (sizeof($usuario) > 0) $_SESSION["usuario"] = $usuario;
    else logout();
}

<?php

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

<?php

//constantes
define("TABELA_PRODUTO", "tb_produtos");
define("TABELA_CATEGORIA", "tb_categorias");
define("TABELA_FORMATOS", "tb_formatos_pacote");
define("TABELA_USUARIOS", "tb_usuarios");

function conectar()
{
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $banco_de_dados = "db_ecommerce";

    $conexao = mysqli_connect($servidor, $usuario, $senha, $banco_de_dados);
    if ($conexao) {
        return $conexao;
    } else {
        die("Conexão falhou: " . mysqli_connect_error());
    }
}

function desconectar($conexao)
{
    if ($conexao) mysqli_close($conexao);
}

function select(string $sql)
{
    $conexao = conectar();
    $resultado = array();

    if ($resultado_query = mysqli_query($conexao, $sql)) {
        while ($linha = mysqli_fetch_assoc($resultado_query)) {
            array_push($resultado, $linha);
        }
        desconectar($conexao);
        return $resultado;
    }
    desconectar($conexao);
    return false;
}

function query(string $sql)
{
    $conexao = conectar();
    $resultado = mysqli_query($conexao, $sql);
    desconectar($conexao);
    return $resultado;
}

function string_segura($string)
{
    $conexao = conectar();
    $resultado = mysqli_real_escape_string($conexao, $string);
    desconectar($conexao);
    return $resultado;
}

<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "servidor.php";

$app->get("/admin", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    global $view;
    return $view->render($response, "admin/index.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Administração"
    ]);
});

$app->get("/admin/categorias", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT nome, id FROM $tabela_categorias;";
    $categorias = select($sql);

    global $view;
    return $view->render($response, "admin/categorias.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Administração - Categorias",
        "categorias" => $categorias
    ]);
});

$app->get("/admin/categorias/cadastrar", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    global $view;
    return $view->render($response, "admin/categorias/cadastro.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Cadastro - Categorias"
    ]);
});

$app->post("/admin/categorias/cadastrar", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));

    if ($nome == "" || $nome == " " || $nome == null) {
        header("Location: /admin/categorias/cadastrar");
        return;
    }

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "INSERT INTO $tabela_categorias (nome) VALUES ('$nome');";
    if(query($sql)) novo_alerta("success", "Categoria cadastrada com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");

    header("Location: /admin/categorias");
    return;
});

$app->post("/admin/categorias/deletar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "DELETE FROM $tabela_categorias WHERE id = $id;";
    if (query($sql)) novo_alerta("success", "Categoria deletada com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");
    return;
});

$app->get("/admin/categorias/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT * FROM $tabela_categorias WHERE id = $id;";
    $categoria = select($sql)[0];

    global $view;
    return $view->render($response, "admin/categorias/editar.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Cadastro - Categorias",
        "categoria" => $categoria
    ]);
});

$app->post("/admin/categorias/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "UPDATE $tabela_categorias SET nome = '$nome' WHERE id = $id;";
    if (query($sql)) novo_alerta("success", "Categoria atualizada com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");

    header("Location: /admin/categorias");
});

$app->get("/admin/produtos", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos;";
    $produtos = select($sql);

    global $view;
    return $view->render($response, "admin/produtos.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Administração - Produtos",
        "produtos" => $produtos
    ]);
});

$app->get("/admin/produtos/cadastrar", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $tabela_formatos = TABELA_FORMATOS;
    $sql = "SELECT * FROM $tabela_formatos;";
    $formatos = select($sql);

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT * FROM $tabela_categorias;";
    $categorias = select($sql);

    global $view;
    return $view->render($response, "admin/produtos/cadastro.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Cadastro - Produtos",
        "formatos" => $formatos,
        "categorias" => $categorias
    ]);
});

$app->post("/admin/produtos/cadastrar", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));
    $id_categoria = string_segura($dados["categoria"]);
    $id_formato = string_segura($dados["formato"]);
    $descricao = string_segura($dados["descricao"]);
    $link_foto = string_segura($dados["link_foto"]);
    $preco = string_segura($dados["preco"]);
    $peso = string_segura($dados["peso"]);
    $altura = string_segura($dados["altura"]);
    $largura = string_segura($dados["largura"]);
    $comprimento = string_segura($dados["comprimento"]);
    $diametro = string_segura($dados["diametro"]);
    $estoque = string_segura($dados["estoque"]);

    if ($nome == "" || $nome == " " || $nome == null) {
        novo_alerta("danger", "Verifique se todos os campos obrigatórios foram preenchidos.");
        header("Location: /admin/produtos/cadastrar");
        return;
    }

    $tabela_produtos = TABELA_PRODUTO;
    $sql = "INSERT INTO $tabela_produtos\n"
        . "(id_categoria, id_formato, nome, descricao, link_foto, preco, peso, altura, largura, comprimento, diametro, estoque)\n"
        . "VALUES($id_categoria, $id_formato, '$nome', '$descricao', '$link_foto', $preco, $peso, $altura, $largura, $comprimento, $diametro, $estoque);";
    if (query($sql)) novo_alerta("success", "Produto cadastrado com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");

    header("Location: /admin/produtos");
    return;
});

$app->post("/admin/produtos/deletar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_produtos = TABELA_PRODUTO;
    $sql = "DELETE FROM $tabela_produtos WHERE id = $id;";
    if (query($sql)) novo_alerta("success", "Produto deletado com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");
    return;
});

$app->get("/admin/produtos/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos WHERE id = $id;";
    $produto = select($sql)[0];

    $tabela_formatos = TABELA_FORMATOS;
    $sql = "SELECT * FROM $tabela_formatos;";
    $formatos = select($sql);

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT * FROM $tabela_categorias;";
    $categorias = select($sql);

    global $view;
    return $view->render($response, "admin/produtos/editar.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Cadastro - Produtos",
        "produto" => $produto,
        "categorias" => $categorias,
        "formatos" => $formatos
    ]);
});

$app->post("/admin/produtos/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }
    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));
    $id_categoria = string_segura($dados["categoria"]);
    $id_formato = string_segura($dados["formato"]);
    $descricao = string_segura($dados["descricao"]);
    $link_foto = string_segura($dados["link_foto"]);
    $preco = string_segura($dados["preco"]);
    $peso = string_segura($dados["peso"]);
    $altura = string_segura($dados["altura"]);
    $largura = string_segura($dados["largura"]);
    $comprimento = string_segura($dados["comprimento"]);
    $diametro = string_segura($dados["diametro"]);
    $estoque = string_segura($dados["estoque"]);

    if ($nome == "" || $nome == " " || $nome == null) {
        header("Location: /admin/produtos/cadastrar");
        return;
    }

    $tabela_produtos = TABELA_PRODUTO;
    $sql =  "UPDATE $tabela_produtos\n"
        . "SET id_categoria=$id_categoria, id_formato=$id_formato, nome='$nome',\n"
        . "descricao='$descricao', link_foto='$link_foto', preco=$preco, peso=$peso,\n"
        . "altura=$altura, largura=$largura, comprimento=$comprimento, diametro=$diametro, estoque=$estoque\n"
        . "WHERE id=$id;";

    if (query($sql)) novo_alerta("success", "Produto atualizado com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");

    header("Location: /admin/produtos");
    return;
});

$app->get("/admin/usuarios", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "SELECT * FROM $tabela_usuarios;";
    $usuarios = select($sql);

    global $view;
    return $view->render($response, "admin/usuarios.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Administração - Usuarios",
        "usuarios" => $usuarios
    ]);
});

$app->post("/admin/usuarios/deletar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "DELETE FROM $tabela_usuarios WHERE id = $id;";
    if (query($sql)) novo_alerta("success", "Usuário deletado com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");
    return;
});

$app->get("/admin/usuarios/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "SELECT * FROM $tabela_usuarios WHERE id = $id;";
    $usuario_selecionado = select($sql)[0];

    global $view;
    return $view->render($response, "admin/usuarios/editar.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Editar - Usuario",
        "usuario_selecionado" => $usuario_selecionado
    ]);
});

$app->post("/admin/usuarios/editar/{id}", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }
    if (!administrador()) {
        header("Location: /");
        return;
    }

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));
    $sobrenome = strtoupper(string_segura($dados["sobrenome"]));
    $usuario = string_segura($dados["usuario"]);
    $cep = (int) $dados["cep"];
    $numero_residencia = (int) $dados["residencia"];
    $admin = (int) $dados["admin"];

    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "UPDATE $tabela_usuarios SET"
        . "\nusuario = '$usuario', nome = '$nome', sobrenome = '$sobrenome', cep = $cep, numero_residencia = $numero_residencia, admin = $admin"
        . "\nWHERE id = $id;";
    if (query($sql)) novo_alerta("success", "Usuário atualizado com sucesso.");
    else novo_alerta("danger", "Algo deu errado. Tente novamente mais tarde.");

    header("Location: /admin/usuarios/editar/$id");
    return;
});

$app->run();

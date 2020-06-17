<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "servidor.php";

$app->get("/", function (Request $request, Response $response, $args) {

    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos ORDER BY data_criacao DESC LIMIT 9;";
    $produtos = select($sql);

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT nome, id FROM $tabela_categorias;";
    $categorias = select($sql);

    global $view;
    return $view->render($response, "index.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Índice",
        "categorias" => $categorias,
        "produtos" => $produtos
    ]);
});

$app->post("/carrinho", function (Request $request, Response $response, $args) {
    novo_alerta("info", "A função de compra ainda não está completa. Nos desculpe pelo transtorno.");
    header("Location: /");
});

$app->get("/produto/{id}", function (Request $request, Response $response, $args) {

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id = $args["id"];
    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos where id = $id;";
    $produto = select($sql)[0];

    if (!$produto) {
        novo_alerta("danger", "Produto inexistente.");
        header("Location: /");
        return;
    }

    global $view;
    return $view->render($response, "produto.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => $produto["nome"],
        "produto" => $produto
    ]);
});

$app->get("/pesquisa", function (Request $request, Response $response, $args) {

    $dados = $request->getQueryParams();
    $pesquisa = string_segura($dados["pesquisa"]);

    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT nome, id FROM $tabela_categorias;";
    $categorias = select($sql);

    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos where nome like '%$pesquisa%' or descricao  like '%$pesquisa%';";
    $produtos = select($sql);

    global $view;
    return $view->render($response, "pesquisa.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Pesquisar Produto",
        "categorias" => $categorias,
        "produtos" => $produtos,
        "pesquisa" => $pesquisa
    ]);
});

$app->get("/categoria/{id}", function (Request $request, Response $response, $args) {

    if (!is_numeric($args["id"])) {
        novo_alerta("danger", "Id tem que ser número.");
        header("Location: /");
        return;
    }

    $id_categoria = $args["id"];
    $tabela_categorias = TABELA_CATEGORIA;
    $sql = "SELECT nome, id FROM $tabela_categorias where id = $id_categoria;";
    $categoria = select($sql)[0];

    if (!$categoria) {
        novo_alerta("danger", "Categoria inexistente.");
        header("Location: /");
        return;
    }

    $sql = "SELECT nome, id FROM $tabela_categorias;";
    $categorias = select($sql);

    $tabela_produtos = TABELA_PRODUTO;
    $sql = "SELECT * FROM $tabela_produtos where id_categoria = $id_categoria;";
    $produtos = select($sql);

    global $view;
    return $view->render($response, "categoria.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => $categoria["nome"],
        "categoria" => $categoria,
        "categorias" => $categorias,
        "produtos" => $produtos
    ]);
});

$app->get("/cadastro", function (Request $request, Response $response, $args) {
    if (logado()) {
        novo_alerta("success", "Você já está cadastrado.");
        header("Location: /");
        return;
    }

    global $view;
    return $view->render($response, "cadastro.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Cadastro"
    ]);
});

$app->post("/cadastro", function (Request $request, Response $response, $args) {

    if (logado()) {
        novo_alerta("success", "Você já está cadastrado.");
        header("Location: /");
        return;
    }

    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));
    $sobrenome = strtoupper(string_segura($dados["sobrenome"]));
    $usuario = string_segura($dados["usuario"]);
    $senha = password_hash($dados["senha"], PASSWORD_DEFAULT);
    $cep = (int) $dados["cep"];
    $numero_residencia = (int) $dados["residencia"];

    if (
        $nome       == "" || $nome      == " " || $nome      == null ||
        $sobrenome  == "" || $sobrenome == " " || $sobrenome == null ||
        $usuario    == "" || $usuario   == " " || $usuario   == null ||
        $senha      == "" || $senha     == " " || $senha     == null
    ) {
        novo_alerta("danger", "Verifique se todos os campos obrigatórios foram preenchidos.");
        header("Location: /cadastro");
        return;
    }

    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "INSERT INTO $tabela_usuarios"
        . "\n(usuario, senha, nome, sobrenome, cep, numero_residencia)"
        . "\nVALUES('$usuario', '$senha', '$nome', '$sobrenome', $cep, $numero_residencia);";
    if (query($sql)) {
        novo_alerta("success", "Registrado com sucesso.");
        header("Location: /login");
        return;
    } else {
        novo_alerta("danger", "Algo deu errado, tente novamente mais tarde.");
        header("Location: /cadastro");
        return;
    }
});

$app->get("/perfil", function (Request $request, Response $response, $args) {
    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }

    global $view;
    return $view->render($response, "perfil.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Perfil"
    ]);
});

$app->post("/perfil", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }

    $id = $_SESSION["usuario"]["id"];
    $dados = $request->getParsedBody();
    $nome = strtoupper(string_segura($dados["nome"]));
    $sobrenome = strtoupper(string_segura($dados["sobrenome"]));
    $usuario = string_segura($dados["usuario"]);
    $cep = (int) $dados["cep"];
    $numero_residencia = (int) $dados["residencia"];

    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "UPDATE $tabela_usuarios SET"
        . "\nusuario = '$usuario', nome = '$nome', sobrenome = '$sobrenome', cep = $cep, numero_residencia = $numero_residencia"
        . "\nWHERE id = $id;";

    if (query($sql)) novo_alerta("success", "Dados atualizados com sucesso.");
    else novo_alerta("danger", "Algo deu errado, tente novamente mais tarde.");

    header("Location: /perfil");
    return;
});

$app->post("/perfil/senha", function (Request $request, Response $response, $args) {

    atualiza_usuario();
    if (!logado()) {
        novo_alerta("info", "Logue-se primeiro.");
        header("Location: /login");
        return;
    }

    $id = $_SESSION["usuario"]["id"];
    $dados = $request->getParsedBody();
    $senha = $dados["senha"];

    if (!password_verify($senha, $_SESSION["usuario"]["senha"])) {
        novo_alerta("danger", "Senha Incorreta");
        header("Location: /perfil");
        return;
    }

    $nova_senha = password_hash($dados["nova_senha"], PASSWORD_DEFAULT);
    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "UPDATE $tabela_usuarios SET"
        . "\nsenha = '$nova_senha'"
        . "\nWHERE id = $id;";

    if (query($sql)) novo_alerta("success", "Senha alterada com sucesso.");
    else novo_alerta("danger", "Algo deu errado, tente novamente mais tarde.");

    header("Location: /perfil");
    return;
});

$app->get("/login", function (Request $request, Response $response, $args) {
    if (logado()) {
        novo_alerta("success", "Você já está logado.");
        header("Location: /");
        return;
    }

    global $view;
    return $view->render($response, "login.html", [
        "usuario" => $_SESSION["usuario"] ?? [],
        "alerta" => pegar_alerta(),
        "title" => "Login"
    ]);
});

$app->post("/login", function (Request $request, Response $response, $args) {
    if (logado()) {
        novo_alerta("success", "Você já está logado.");
        header("Location: /");
        return;
    }

    $dados = $request->getParsedBody();
    $usuario = string_segura($dados["usuario"]);
    $senha = $dados["senha"];

    $tabela_usuarios = TABELA_USUARIOS;
    $sql = "SELECT * FROM $tabela_usuarios where usuario = '$usuario';";
    $usuario = select($sql)[0];

    if ($usuario == false || sizeof($usuario) == 0 || !password_verify($senha, $usuario["senha"])) {
        novo_alerta("danger", "Usuário ou senha incorretos.");
        header("Location: /login");
        return;
    }

    $_SESSION["usuario"] = $usuario;
    $_SESSION["logado"] = true;
    header("Location: /perfil");
});

$app->get("/logout", function (Request $request, Response $response, $args) {
    logout();
    header("Location: /");
});
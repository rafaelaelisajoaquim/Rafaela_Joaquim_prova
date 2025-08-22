<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O Produto TEM PERMISSÃO
    if ($_SESSION['perfil'] != 1  && $_SESSION['perfil']!=3)  {
        echo "Acesso negado!";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_prod = $_POST['nome_prod'];
        $descricao = $_POST['descricao'];
        $qtde = $_POST['qtde'];
        $valor_unit = $_POST['valor_unit'];

        $sql = "INSERT INTO produto (nome_prod, descricao, qtde, valor_unit) VALUES (:nome_prod, :descricao, :qtde, :valor_unit)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_prod', $nome_prod, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':qtde', $qtde, PDO::PARAM_INT);
        $stmt->bindParam(':valor_unit', $valor_unit, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>alert('Produto cadastrado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar Produto!');</script>";
        }
    }

    //OBTENDO O nome_produto DO PERFIL DO USUARIO LOGADO
    $id_perfil = $_SESSION['perfil'];
    $sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
    $stmtPerfil=$pdo->prepare($sqlPerfil);
    $stmtPerfil->bindParam(':id_perfil',$id_perfil);
    $stmtPerfil->execute();
    $perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil = $perfil['nome_perfil'];

    //DEFINICAO DAS PERMISSOES POR perfil
    $permissoes= [
        1 =>["Cadastrar"=>["cadastro_usuario.php","cadastro_perfil.php","cadastro_cliente.php","cadastro_fornecedor.php","cadastro_produto.php","cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php","buscar_perfil.php","buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php","buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php","alterar_perfil.php","alterar_cliente.php","alterar_fornecedor.php","alterar_produto.php","alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php","excluir_perfil.php","excluir_cliente.php","excluir_fornecedor.php","excluir_produto.php","excluir_funcionario.php"]],
   
        2 =>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php","alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],
   
        3 =>["Cadastrar"=>["cadastro_fornecedor.php","cadastro_produto.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],
   
        4 =>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php"]],      
    ];

    //OBTENDO AS OPÇÕES DISPONIVEIS PARA O PERFIL LOGADO
    $opcoes_menu = $permissoes["$id_perfil"];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Cadastrar Produto </title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .voltar{
            background-color: #007bff;
            padding: 8px 15px;
            color: white;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
        }

        .footer {
            background-color: #333;
            color:white;
            padding: 15px;
            margin-top: 20px;
            font-size:14px;
        }

        input[type="number"]{
        width: 80%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        }
    </style>
</head>
    <body>
        <nav>
            <ul class="menu">
                    <?php foreach($opcoes_menu as $categoria => $arquivos): ?>
                        <li class="dropdown">
                            <a href="#"><?=$categoria ?></a>
                            <ul class="dropdown-menu">
                                <?php foreach($arquivos as $arquivo): ?>
                                    <li>
                                        <a href="<?=$arquivo ?>">
                                        <?=ucfirst(str_replace("_"," ",basename($arquivo,".php"))); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>    
            </nav>
<h2>Cadastrar Produto</h2>
<form id="formCadastro" action="cadastro_produto.php" method="POST">
   
    <label for="nome_prod">Nome do produto:</label>
    <input type="text" name="nome_prod" id="nome_prod" minlength="3" required>


    <label for="descricao">Descrição:</label>
    <input type="text" name="descricao" id="descricao" required>


    <label for="qtde">Quantidade:</label>
    <input type="number" id="qtde" name="qtde" required>


    <label for="valor_unit">Valor Unitário:</label>
    <input type="number" id="valor_unit" name="valor_unit" min="0" step="0.01" required>


    <button type="submit">Salvar</button>
    <button type="reset">Cancelar</button>
</form>

<a class="voltar" href="principal.php">Voltar</a>

        <script>
        const formCadastro = document.getElementById("formCadastro");

        formCadastro.addEventListener("submit", function(e) {
            const descricao = document.getElementById("descricao").value.trim();
            const quantidade = document.getElementById("qtde").value.trim();

            // Validação da descrição
            if(descricao.length < 3){
                alert("A descrição deve ter pelo menos 3 caracteres.");
                e.preventDefault();
                return;
            }

            // Validação da quantidade
            if(!/^\d+$/.test(quantidade) || parseInt(quantidade) < 1){
                alert("Digite uma quantidade válida (número inteiro maior que 0).");
                e.preventDefault();
                return;
            }
        });
        </script>

        <footer class="footer">
            <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
        </footer>
    </body>
</html>





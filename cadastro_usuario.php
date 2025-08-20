<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O USUÁRIO TEM PERMISSÃO
    // SUPONDO QUE O PERFIL 1 SEJA O ADMINISTRADOR
    if ($_SESSION['perfil'] != 1) {
        echo "Acesso negado!";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $id_perfil = $_POST['id_perfil'];

        $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':id_perfil', $id_perfil);

        if ($stmt->execute()) {
            echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar usuário!');</script>";
        }
    }

    //OBTENDO O NOME DO PESFIL DO USUARIO LOGADO
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
    <title> Cadastrar Usuário </title>
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
        <h2> Cadastrar Usuário </h2>
        <form id="formCadastro" action="cadastro_usuario.php" method="POST">
            <label for="nome"> Nome: </label>
            <input type="text" name="nome" id="nome" required>

            <label for="email"> E-mail: </label>
            <input type="email" name="email" id="email" required>

            <label for="senha"> Senha: </label>
            <input type="password" id="senha" name="senha" required>

            <label for="id_perfil"> Perfil: </label>
            <select name="id_perfil" id="id_perfil" required>
                <option value="1"> Administrador </option>
                <option value="2"> Secretária </option>
                <option value="3"> Funcionário </option>
                <option value="4"> Cliente </option>
            </select>

            <button type="submit"> Salvar </button>
            <button type="reset"> Cancelar </button>
        </form>

        <a class="voltar" href="principal.php">Voltar</a>

        <script>
            const nomeInput = document.getElementById("nome");

            // Bloquear símbolos e números no campo nome
            nomeInput.addEventListener("input", function() {
                this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
            });

            document.getElementById("formCadastro").addEventListener("submit", function(e) {
                const nome = nomeInput.value.trim();
                const email = document.getElementById("email").value.trim();
                const senha = document.getElementById("senha").value; // <<< pegando o valor da senha

                // Validação do nome (mínimo 3 letras)
                if (nome.length < 3) {
                    alert("O nome deve ter pelo menos 3 letras.");
                    e.preventDefault(); 
                    return;
                }

                // Validação do email com regex simples
                const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regexEmail.test(email)) {
                    alert("Digite um e-mail válido.");
                    e.preventDefault();
                    return;
                }

                // Validação da senha (mínimo 8 caracteres)
                if (senha.length < 8) {
                    alert("A senha deve ter pelo menos 8 caracteres.");
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

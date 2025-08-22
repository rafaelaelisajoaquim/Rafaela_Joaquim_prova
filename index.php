<?php
    session_start();
    require_once 'conexao.php';

    if($_SERVER['REQUEST_METHOD']=="POST"){
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email',$email);
        $stmt->execute();
        $usuario=$stmt->fetch(PDO::FETCH_ASSOC);

        if($usuario && password_verify($senha,$usuario['senha'])){

            //LOGIN BEM SUCEDIDO DEFINE VARIAVEIS DE SESSAO
            $_SESSION['usuario']=$usuario['nome'];
            $_SESSION['perfil']=$usuario['id_perfil'];
            $_SESSION['id_usuario']=$usuario['id_usuario'];

            //VERIFICA SE A SENHA É TEMPORARIA
            if($usuario['senha_temporaria']){

                //REDIRECIONA PARA A TROCA DE SENHA
                header("Location: alterar_senha.php");
                exit();
            } else {
                //REDIRECIONA PARA A PAGINA PRINCIPAL
                header("Location: principal.php");
                exit();
            }
        } else {
            //LOGIN INVALIDO
            echo"<script>alert('E-mail ou senha incorretos');
            window.location.href='index.php';</script>";
        } 
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .botao_senha {
            background-color: #007bff;
            padding: 8px 15px;
            color: white;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
        } 

        .footer {
            background-color: #333;
            font-size: 14px;
            color: white;
            padding: 10px;
            margin-top: 125px;
            margin-left: -8px;
            margin-right: -8px;
        }
    </style>
</head>
    <body>
        <h2>Login</h2>
        <form action="index.php" method="POST">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>

        <p><a class="botao_senha" href="recuperar_senha.php">Esqueci a senha</a></p>
        
        <footer class="footer">
                <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
        </footer>
    </body>
</html>
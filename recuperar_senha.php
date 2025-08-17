<?php
    session_start();
    require_once 'conexao.php';
    require_once 'funcoes_email.php';  // ARQUIVO COM AS FUNÇÕES QUE GERAM A SENHA E SIMULAM O ENVIO

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];

        // VERIFICA SE O EMAIL EXISTE NO BANCO DE DADOS
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // GERA UMA SENHA TEMPORÁRIA E ALEATÓRIA
            $senha_temporaria = gerarSenhaTemporaria();
            $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

            // ATUALIZA A SENHA NO BANCO DE DADOS
            $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // SIMULA O ENVIO DO EMAIL (GRAVA EM TXT)
            simularEnvioEmail($email, $senha_temporaria);
            echo "<script>alert('Uma senha temporária foi gerada e enviada (simulação). Verifique o arquivo emails_simulados.txt');window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Email não encontrado.');</script>";
        }
    } 
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Recuperar Senha </title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .voltar{
        padding: 8px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-family: Arial, sans-serif;
        text-decoration: none;
    } 

    .footer {
            background-color: #333;
            padding: 20px;
            margin-top: auto;
            margin-left: -10px;
            margin-right: -10px;
            width: calc(100% + 20px);
            box-sizing: border-box;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .footer p {
            text-align: center;
            color: white;
            margin: 0;
            font-size: 14px;
        } 
    </style>
</head>
<body>
    <h2> Recuperar Senha </h2>

    <form action="recuperar_senha.php" method="POST">
        <label for="email"> Digite seu e-mail cadastrado: </label>
        <input type="email" id="email" name="email" required />

        <button type="submit">Enviar senha temporária</button>
    </form>
    <a class="voltar" href="index.php">Voltar</a>

        <footer class="footer">
                <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
        </footer>
    </body>
</html>
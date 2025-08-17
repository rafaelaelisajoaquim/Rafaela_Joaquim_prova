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
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Cadastrar Usuário </title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            position: relative;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            padding-bottom: 80px;
        }

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
            margin-top: 30px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 0;
            position: absolute;
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
    <h2> Cadastrar Usuário </h2>
    <form action="cadastro_usuario.php" method="POST">
        <label for="nome"> Nome: </label>
        <input type="text" name="nome" id="nome" required>

        <label for="email"> E-mail: </label>
        <input type="email" name="email" id="email" required>

        <label for="senha"> Senha: </label>
        <input type="password" name="senha" id="senha" required>

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
    <footer class="footer">
    <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
    </footer>
</body>
</html>
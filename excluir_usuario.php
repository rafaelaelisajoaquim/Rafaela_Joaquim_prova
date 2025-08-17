<?php
    session_start();
    require_once 'conexao.php';

    //VERIFICA SE O USUARIO TEM PERMISSAO DE adm
    if($_SESSION['perfil'] !=1){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    }

    //INICIALIZA VARIAVEL PARA ARMAZENAR USUARIOS
    $usuarios = [];

    //BUSCA TODOS OS USUARIOS CADASTRADOS EM ORDEM ALFABETICA
    $sql = "SELECT * FROM usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //SE UM ID FOR PASSADO VIA GET EXCLUI O USUARIO
    if(isset($_GET['id']) && is_numeric($_GET['id'])){
        $id_usuario = $_GET['id'];

        //EXCLUI O USUARIO DO BANCO DE DADOS
        $sql = "DELETE FROM usuario WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_usuario);
       
        if($stmt->execute()){
            echo "<script>alert('Usuário excluido com sucesso!');window.location.href='excluir_usuario.php';</script>";
        } else {
        echo "<script>alert('Erro ao excluir usuário!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>
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

        table {
            background-color: white;
            border-radius: 10px;
            margin: 0 auto;
            margin: 20px auto;
            border-collapse: collapse;
            width: 70%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        th, td, tr {
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #007bff;
            color: white;
            font-size: 14px;
            text-transform: uppercase;
        }

        td {
            background-color: white;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .acoes a {
            text-decoration: none;
        }

        .voltar{
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        } 
        
        .footer {
            background-color: #333;
            padding: 20px;
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
        <h2>Excluir Usuário</h2>
            <?php if(!empty($usuarios)): ?>
                <table> 
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id_usuario'])?></td>
                            <td><?= htmlspecialchars($usuario['nome'])?></td>
                            <td><?= htmlspecialchars($usuario['email'])?></td>
                            <td><?= htmlspecialchars($usuario['id_perfil'])?></td>
                            <td class="acoes">
                                <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
        <p>Nenhum usuário encontrado</p>
    <?php endif; ?>
<a class="voltar" href="principal.php">Voltar</a>
        
        <footer class="footer">
            <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
        </footer>
    </body>
</html>
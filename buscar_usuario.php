<?php
    session_start();
    require_once 'conexao.php';

    //VERIFICA SE O USUÁRIO TEM PERMISSÃO DE adm OU secretaria
    if($_SESSION['perfil'] !=1 && $_SESSION['perfil']!=2){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    }

    $usuario = []; //INICIALIZA A VARIAVEL PARA EVITAR ERROS
    //SE O FORMULÁRIO FOR ENVIADO, BUSCA OPELO id OU nome
    if($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['busca'])){
        $busca = trim($_POST['busca']);

        //VERIFICA SE A BUSCA É UM NUMERO OU UM NOME
        if(is_numeric($busca)){
            $sql = "SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
                $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
                $stmt=$pdo->prepare($sql);
                $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
        }
    } else{
        $sql = "SELECT * FROM usuario ORDER BY nome ASC";
        $stmt=$pdo->prepare($sql);
    }
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <style>

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        text-align: center;
    }

    table {
        background-color:#b6e9fd;
        border-radius: 10px;
        padding: 8px 15px;
        margin: 0 auto;
        margin:20px auto;
        border-collapse: collapse;
        overflow: hidden;
        width: 50%;
    }

    th, td, tr {
        text-align: center;
        padding: 12px;
    }

    th {
        background-color:rgb(119, 173, 238);
    }
    
    td {
        background-color: 
    }


    .voltar{
    padding: 8px 15px;
    background-color:#b6e9fd;
    color: black;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    text-decoration: none;
    } 
    </style>

</head>
<body>
    <h2>Lista de Usuários</h2>
        <form action="buscar_usuario.php" method="POST">
            <label for="busca">Digite o ID ou NOME (opcional)</label>
            <input type="text" id="busca" name="busca">
            <button type="submit">Pesquisar</button>
        </form>
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
                    <td>
                        <a href="alterar_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>">Alterar</a>

                        <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>
                        "onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else:?>
            <p>Nenhum usuário encontrado</p>
        <?php endif;?></br>
            <a class="voltar" href="principal.php">Voltar</a>
</body>
</html>
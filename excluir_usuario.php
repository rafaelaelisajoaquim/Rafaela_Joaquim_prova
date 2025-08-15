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
</head>
<body>
    <h2>Excluir Usuário</h2>
    <?php if(!empty($usuarios)): ?>
        <table border='1'> 
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
                            <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum usuário encontrado</p>
<?php endif; ?>
        <a href="principal.php">Voltar</a>
</body>
</html>
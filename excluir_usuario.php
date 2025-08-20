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
            echo "<script>alert('Usuário excluído com sucesso!');window.location.href='excluir_usuario.php';</script>";
        } else {
        echo "<script>alert('Erro ao excluir usuário!');</script>";
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
    <title>Excluir Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
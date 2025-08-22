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
    <title>Buscar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    table {
            background-color: white;
            border-radius: 10px;
            margin: 0 auto;
            margin: 20px auto;
            border-collapse: collapse;
            overflow: hidden;
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
            font-size: 14px;
            color: white;
            padding: 10px;
            margin-top: 30px;
            margin-left: -8px;
            margin-right: -8px;
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
        <div class="content">
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
                                        <td class="acoes">
                                            <a href="alterar_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>">Alterar</a>
                                            |
                                            <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>"
                                            onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else:?>
                    <p>Nenhum usuário encontrado</p>
                <?php endif;?>
                <a class="voltar" href="principal.php">Voltar</a>
        </div>
        <footer class="footer">
            <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
        </footer>
    </body>
</html>
<?php
    session_start();
    require_once 'conexao.php';

    //VERIFICA SE O USUÁRIO TEM PERMISSÃO DE adm
    if($_SESSION['perfil'] !=1  && $_SESSION['perfil']!=2 && $_SESSION['perfil']!=3){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    }

    //INICIALIZA VARIÁVEIS
    $produto = null;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['busca_produto'])){
            $busca = trim($_POST['busca_produto']);

            //VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM nome
            if(is_numeric($busca)){
                $sql = "SELECT * FROM produto WHERE id_produto = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM produto WHERE nome_prod LIKE :busca_nome";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            //SE O produto NAO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$produto){
                echo "<script>alert('Produto não encontrado!');</script>";
            }
        }
    }

           //OBTENDO O NOME DO PESFIL DO produto LOGADO
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
    <title>Alterar Produto</title>
    <link rel="stylesheet" href="styles.css">
    <!-- CERTIFIQUE-SE DE QUE O JS ESTEJA SENDO CARREGADO CORRETAMENTE -->
    <script src="script.js"></script>
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
            font-size: 14px;
            color: white;
            padding: 15px;
            margin-top: 170px;
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
    <h2>Alterar Produto</h2>
        <form action="alterar_produto.php" method="POST">
            <label for="busca_produto">Digite o ID ou NOME do usuário</label>
            <input type="text" id="busca_produto" name="busca_produto" required onkeyup="buscarSugestoes()">

            <!-- div PARA SUGESTÕES DE USUÁRIOS -->
            <div id="sugestoes"></div>
            <button type="submit">Buscar</button>
        </form> 

        <?php if($produto): ?>
            <!-- FORM PARA ALTERAR produto -->
            <form id="formProduto" action="processa_alteracao_produto.php" method="POST">
            <input type="hidden" name="id_produto" value="<?=htmlspecialchars($produto['id_produto'])?>">

            <label for="nome_prod">Nome do Produto:</label>
            <input type="text" id="nome_prod" minlength="3" name="nome_prod" value="<?=htmlspecialchars($produto['nome_prod'])?>" required>
            
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?=htmlspecialchars($produto['descricao'])?>" required>

            <label for="qtde"> Quantidade: </label>
            <input type="number" id="qtde" name="qtde" value="<?=htmlspecialchars($produto['qtde'])?>" required>

            <label for="valor_unit"> Valor Unitário: </label>
            <input type="number" id="valor_unit" name="valor_unit" min="0" step="0.01" value="<?=htmlspecialchars($produto['valor_unit'])?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
            <?php endif; ?>
            <a class="voltar" href="principal.php">Voltar</a>

        <script>
            formProduto.addEventListener("submit", function(e) {
                const descricao = document.getElementById("descricao").value.trim();

                if(descricao.length < 3){
                alert("A descrição deve ter pelo menos 3 caracteres.");
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
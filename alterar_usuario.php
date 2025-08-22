<?php
    session_start();
    require_once 'conexao.php';

    //VERIFICA SE O USUÁRIO TEM PERMISSÃO DE adm
    if($_SESSION['perfil'] !=1){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    }

    //INICIALIZA VARIÁVEIS
    $usuario = null;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['busca_usuario'])){
            $busca = trim($_POST['busca_usuario']);

            //VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM nome
            if(is_numeric($busca)){
                $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            //SE O USUARIO NAO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$usuario){
                echo "<script>alert('Usuário não encontrado!');</script>";
            }
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
    <title>Alterar Usuário</title>
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
            padding: 10px;
            margin-top: 150px;
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
    <h2>Alterar Usuário</h2>
        <form action="alterar_usuario.php" method="POST">
            <label for="busca_usuario">Digite o ID ou NOME do usuário</label>
            <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">

            <!-- div PARA SUGESTÕES DE USUÁRIOS -->
            <div id="sugestoes"></div>
            <button type="submit">Buscar</button>
        </form> 

        <?php if($usuario): ?>
            <!-- FORM PARA ALTERAR USUARIO -->
            <form id="formAltera" action="processa_alteracao_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>

            <label for="id_perfil">Perfil:</label>
            <select id="id_perfil" name="id_perfil">
                <option value="1"<?=$usuario['id_perfil']== 1 ?'select':''?>>Administrador</option>
                <option value="2"<?=$usuario['id_perfil']== 2 ?'select':''?>>Secretária</option>
                <option value="3"<?=$usuario['id_perfil']== 3 ?'select':''?>>Almoxarife</option>
                <option value="4"<?=$usuario['id_perfil']== 4 ?'select':''?>>Cliente</option>
            </select>

        <!-- SE O USUARIO FOR ADM, EXIBIR OPÇAO DE ALTERAR SENHA -->
         <?php if($_SESSION['perfil'] == 1 ): ?>
                <label for='nova_senha'>Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
            <?php endif; ?>
            <a class="voltar" href="principal.php">Voltar</a>

            <script>
                const nomeInput = document.getElementById("nome");

                // Bloquear símbolos e números no campo nome
                nomeInput.addEventListener("input", function() {
                    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
                });

                document.getElementById("formAltera").addEventListener("submit", function(e) {
                    const nome = nomeInput.value.trim();
                    const email = document.getElementById("email").value.trim();
                    const nova_senhaInput = document.getElementById("nova_senha"); // pega o campo se existir

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
                });
            </script>

    <footer class="footer">
            <p>Rafaela Elisa Joaquim | Desenvolvimento de Sistemas</p>
    </footer>
    </body>
</html>
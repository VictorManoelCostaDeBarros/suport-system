<?php 
    $token = $_GET['token'];
?>
<h2>Visualizando Chamado: <?php echo $_GET['token']; ?></h2>

<hr>


<h3>Pergunta do suporte: <?php echo $info['pergunta']; ?></h3>

<?php 
    $puxarInteracoes = \MySql::conectar()->prepare("SELECT * FROM interacao_chamada WHERE id_chamado = ?");
    $puxarInteracoes->execute(array($token));
    echo '<hr>';
    $puxarInteracoes = $puxarInteracoes->fetchAll();
    foreach ($puxarInteracoes as $key => $value) {
        if($value['admin'] == 1){
            echo '<p><b>Admin: </b>'.$value['mensagem'].'</p>';
        }else{
            echo '<p><b>Você: </b>'.$value['mensagem'].'</p>';
        }
        echo '<hr>';
    }
?>

<?php
    if(isset($_POST['responder_chamado'])){
        $mensagem = $_POST['mensagem'];
        $sql = \MySql::conectar()->prepare("INSERT INTO interacao_chamada VALUES(null,?,?,?,0)");
        $sql->execute(array($token,$mensagem,-1));
        echo '<script>alert("Sua resposta foi envida com sucesso aguarde o admin respondelo")</script>';
        echo '<script>location.href="'.BASE.'chamado?token='.$token.'"</script>';
        die();
    }
    $sql = \MySql::conectar()->prepare("SELECT * FROM interacao_chamada WHERE id_chamado = ? ORDER BY id DESC");
    $sql->execute(array($token));
    if($sql->rowCount() == 0){
        echo '<p>Aguarde até ter um resposata do admin para continuar com suporte.</p>';
    }else{
        $info = $sql->fetchAll();
        if($info[0]['admin'] == -1){
            // A última interação foi feita por quem abri o suporte. Não pode interagir até ter um resposta.
            echo '<p>Aguarde até ter um resposata do admin para continuar com suporte.</p>';
        }else{
            echo '<form method="post">
                <textarea name="mensagem" placeholder="Sua resposta..."></textarea></br>
                <input type="submit" name="responder_chamado" value="Enviar!" />
            </form>';
        }
    }
?>



<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    if(isset($_POST['responder_novo_chamado'])){
        $token = $_POST['token'];
        $email = $_POST['email'];
        $mensagem = $_POST['mensagem'];

        $sql = \MySql::conectar()->prepare("INSERT INTO interacao_chamada VALUES (null,?,?,?,1)");
        $sql->execute(array($token,$mensagem, 1));
        // Envio de email
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'vps.dankicode.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'teste@dankicode.com';                     // SMTP username
            $mail->Password   = 'gui123456';                               // SMTP password
            $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('teste@dankicode.com', 'Guilherme');
            $mail->addAddress($email, 'Joe User');     // Add a recipient


            // Content
            $mail->isHTML(true); 
            $mail->CharSet = "URF-8";                                 // Set email format to HTML
            $mail->Subject = 'Nova interação no chamado: '.$token;
            $url = BASE.'chamado?token='.$token;
            $informacoes = '
            Olá, Uma nova interação foi feita no seu chamado!<br/>
            Ultilize o link abaixo para interagir:</br>
            <a href="'.$url.'">Acessar Chamada</a>
            ';
            $mail->Body    = $informacoes;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        echo '<script>alert("Resposta enviada com sucesso!")</script>';
    }else if(isset($_POST['responder_novo_interacao'])){
        $mensagem = $_POST['mensagem'];
        $token = $_POST['token'];
        $email = \MySql::conectar()->prepare("SELECT * FROM chamados WHERE token = ?");
        $email->execute(array($token));
        $email = $email->fetch()['email'];
        \MySql::conectar()->exec("UPDATE interacao_chamada SET status = 1 WHERE id = $_POST[id]");
        $sql = \MySql::conectar()->prepare("INSERT INTO `interacao_chamada` VALUES (null,?,?,1,1)");
        $sql->execute(array($token,$mensagem));

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'vps.dankicode.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'teste@dankicode.com';                     // SMTP username
            $mail->Password   = 'gui123456';                               // SMTP password
            $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('teste@dankicode.com', 'Guilherme');
            $mail->addAddress($email, 'Joe User');     // Add a recipient


            // Content
            $mail->isHTML(true); 
            $mail->CharSet = "URF-8";                                 // Set email format to HTML
            $mail->Subject = 'Nova interação no chamado: '.$token;
            $url = BASE.'chamado?token='.$token;
            $informacoes = '
            Olá, Uma nova interação foi feita no seu chamado!<br/>
            Ultilize o link abaixo para interagir:</br>
            <a href="'.$url.'">Acessar Chamada</a>
            ';
            $mail->Body    = $informacoes;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        echo '<script>alert("Resposta enviada com sucesso!")</script>';
    }
?>
<style>
    textarea, input{
        width:100%;
    }

    textarea{
        height: 120px;
    }
</style>
<h2>Novos chamados: </h2>
<?php 
    $pegarChamados = \MySql::conectar()->prepare("SELECT * FROM chamados ORDER BY id DESC");
    $pegarChamados->execute();
    $pegarChamados = $pegarChamados->fetchAll();
    foreach ($pegarChamados as $key => $value) {
    $verificaInteracao = \MySql::conectar()->prepare("SELECT * FROM interacao_chamada WHERE id_chamado = '$value[token]'");
    $verificaInteracao->execute();
    if($verificaInteracao->rowCount() >= 1)
        continue;
?>
    <h2><?php echo $value['pergunta']; ?></h2>
    <form method="post">
        <textarea name="mensagem" placeholder="Sua resposta..."></textarea>
        <br>
        <input type="submit" name="responder_novo_chamado" value="Responder!">
        <input type="hidden" name="token" value="<?php echo $value['token']; ?>">
        <input type="hidden" name="email" value="<?php echo $value['email']; ?>">
    </form>
<?php } ?>
<hr>

<h2>Últimas interações:</h2>
<?php 
    $pegarChamados = \MySql::conectar()->prepare("SELECT * FROM interacao_chamada WHERE admin = -1 AND status = 0 ORDER BY id DESC");
    $pegarChamados->execute();
    $pegarChamados = $pegarChamados->fetchAll();
    foreach ($pegarChamados as $key => $value) {

?>
    <h2><?php echo $value['mensagem']; ?></h2>
    <p>CLique <a href="<?php echo BASE ?>chamado?token=<?php echo $value['id_chamado']; ?>">aqui</a> para visualizar esse chamado!</p>
    <form method="post">
        <textarea name="mensagem" placeholder="Sua resposta..."></textarea>
        <br>
        <input type="submit" name="responder_novo_interacao" value="Responder!">
        <input type="hidden" name="id" value="<?php echo $value['id']; ?>">
        <input type="hidden" name="token" value="<?php echo $value['id_chamado']; ?>">
    </form>
<?php } ?>
<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
</head>
<body>
    <style type="text/css">
        input,textarea{
            width:100%;
        }

        textarea{
            height: 120px;
        }
    </style>
    <h2>Abrir chamada!</h2>
    <?php
    if(isset($_POST['acao'])){
        $email = $_POST['email'];
        $pergunta = $_POST['pergunta'];
        $token = md5(uniqid());

        $sql = \MySql::conectar()->prepare("INSERT INTO chamados VALUES (null,?,?,?)");
        $sql->execute(array($email,$pergunta,$token));
        // Enviar e-mail para o usuario que o chamado foi aberto.
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
            $mail->Subject = 'Seu chamado foi aberto!';
            $url = BASE.'chamado?token='.$token;
            $informacoes = '
            Olá, seu chamado foi criado com sucesso!<br/>
            Ultilize o link abaixo para interagir:</br>
            <a href="'.$url.'">Acessar Chamada</a>
            ';
            $mail->Body    = $informacoes;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        echo '<script>alert("Seu chamado foi aberto com sucesso! Você receberá no e-mail as informações para interagir.")</script>';
    }
       
    
?>

    <form method="post">
        <input type="email" name="email" placeholder="Seu e-mail...">
        <br>
        <br>
        <textarea name="pergunta" placeholder="Sua pergunta?"></textarea>
        <br>
        <br>
        <input type="submit" name="acao" value="Enviar!">
    </form>

</body>
</html>





<?php
  namespace Hcode;

  use rain\Tpl;

  class Mailer{
    const USERNAME = "thiagoferreira000000001@gmail.com";
    const PASSWORD = "thiagoferreira00000000";
    const
    public function __construct($toAddress, $toName, $subject, $tplName, $data = array()){

        //Create a new PHPMailer instance
        $mail = new PHPMailer;

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;

        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

<<<<<<< HEAD
          //Tell PHPMailer to use SMTP
          $this->mail->isSMTP();
          

=======
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
>>>>>>> parent of 4f3a5cc... Abre o tpl mas não envia o email

        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';

<<<<<<< HEAD
          //Set the hostname of the mail server
          $this->mail->Host = 'smtp.gmail.com';
          // use
          // $this->mail->Host = gethostbyname('smtp.gmail.com');
          // if your network does not support SMTP over IPv6
          
=======
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
>>>>>>> parent of 4f3a5cc... Abre o tpl mas não envia o email

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = Mailer::USERNAME;

<<<<<<< HEAD
          $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
          //Set the encryption system to use - ssl (deprecated) or tls
          $this->mail->SMTPSecure = 'tls';
=======
        //Password to use for SMTP authentication
        $mail->Password = ;
>>>>>>> parent of 4f3a5cc... Abre o tpl mas não envia o email

        //Set who the message is to be sent from
        $mail->setFrom(USERNAME, 'Thiago Dev');

        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');

        //Set who the message is to be sent to
        $mail->addAddress('thiagofp707@gmail.com', 'Thiago Cliente');

        //Set the subject line
        $mail->Subject = 'Teste PHPMailer';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML(file_get_contents('contents.html'), __DIR__);

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
    }
  }

 ?>

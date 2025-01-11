<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $assunto = $_POST["assunto"];
    $mensagem = $_POST["mensagem"];
    $htmlFile = $_FILES["htmlFile"];

    // Verifica se um arquivo foi enviado
    if ($htmlFile["size"] > 0 && $htmlFile["error"] == UPLOAD_ERR_OK) {
        $htmlContent = file_get_contents($htmlFile["tmp_name"]);

        // Montagem do corpo do e-mail
        $stringData = "<p>" . nl2br(htmlspecialchars($mensagem)) . "</p>";
        $stringData .= $htmlContent; // Adiciona o conteúdo HTML diretamente

        // Cabeçalhos MIME para enviar e-mail em formato HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // Remetente (deve ser um e-mail válido no seu domínio)
        $headers .= "From: Mercado Livre <Mlkchato704@gmail.com>" . "\r\n";
        $headers .= "Reply-To: Mlkchato704@gmail.com" . "\r\n";
        $headers .= "Return-Path: Mlkchato704@gmail.com" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        // Leitura dos e-mails do arquivo db.txt e envio para cada um
        $emails = file("db.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $envios_sucesso = 0;

        foreach ($emails as $email) {
            // Verifica o domínio do e-mail para ajustar os headers
            $domain = substr(strrchr(trim($email), "@terra.com.br"), 1);
            if (in_array($domain, ['hotmail.com', 'terra.com.br'])) {
                $headers .= "X-PHP-Script: " . $_SERVER['PHP_SELF'] . " for $domain\r\n";
            }

            // Envio individual do e-mail
            $envio = mail(trim($email), $assunto, $stringData, $headers);

            if ($envio) {
                $envios_sucesso++;
                sleep(1); // Atraso de 1 segundo entre envios
            }
        }

        // Redirecionamento após o envio
        if ($envios_sucesso > 0) {
            header("Location: sucesso.html");
            exit();
        } else {
            header("Location: erro.html");
            exit();
        }
    } else {
        // Caso nenhum arquivo tenha sido enviado
        header("Location: erro.html");
        exit();
    }
} else {
    // Caso o método de requisição não seja POST
    header("Location: erro.html");
    exit();
}
?>

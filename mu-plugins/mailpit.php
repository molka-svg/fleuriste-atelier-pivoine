<?php
/**
 * Plugin Name: E-mails locaux vers Mailpit
 * Description: En local, envoie les e-mails de WordPress à Mailpit. Inactif si SMTP_HOST n'est pas défini.
 */
add_action('phpmailer_init', function ($mailer) {
    $host = getenv('SMTP_HOST');
    if (!$host) {
        return;
    }
    $mailer->isSMTP();
    $mailer->Host        = $host;
    $mailer->Port        = (int) (getenv('SMTP_PORT') ?: 1025);
    $mailer->SMTPAuth    = false;
    $mailer->SMTPAutoTLS = false;
});

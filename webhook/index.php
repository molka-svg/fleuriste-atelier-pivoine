<?php
/**
 * Bonus : récepteur du webhook WooCommerce « Commande créée ».
 * WooCommerce envoie un POST JSON signé ; on vérifie la signature puis on affiche les commandes reçues.
 * Lancé par docker-compose (service « webhook ») : http://localhost:8090
 */
$secret  = getenv('WEBHOOK_SECRET') ?: '';
$journal = '/tmp/commandes-recues.json';
$recues  = is_file($journal) ? json_decode(file_get_contents($journal), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $corps     = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_WC_WEBHOOK_SIGNATURE'] ?? '';
    // Signature = base64(HMAC-SHA256(corps, secret)) : prouve que l'envoi vient bien de notre boutique
    $valide    = $secret !== '' && hash_equals(base64_encode(hash_hmac('sha256', $corps, $secret, true)), $signature);
    $commande  = json_decode($corps, true);
    if (!isset($commande['id'])) {           // « ping » envoyé par WooCommerce à la création du webhook
        http_response_code(200);
        exit('ping reçu');
    }
    if (($commande['status'] ?? '') === 'checkout-draft') {   // brouillon : la vraie commande suivra
        http_response_code(200);
        exit('brouillon ignoré');
    }
    // Une ligne par commande : si elle a déjà été reçue (autre état), on garde la plus récente
    $recues = array_values(array_filter($recues, fn($r) => $r['numero'] !== $commande['id']));
    array_unshift($recues, [
        'recu_le'   => date('d/m/Y H:i:s'),
        'evenement' => $_SERVER['HTTP_X_WC_WEBHOOK_TOPIC'] ?? '?',
        'numero'    => $commande['id'],
        'client'    => trim(($commande['billing']['first_name'] ?? '') . ' ' . ($commande['billing']['last_name'] ?? '')),
        'total'     => ($commande['total'] ?? '?') . ' ' . ($commande['currency'] ?? ''),
        'paiement'  => $commande['payment_method_title'] ?? '',
        'statut'    => $commande['status'] ?? '',
        'signature' => $valide,
    ]);
    file_put_contents($journal, json_encode(array_slice($recues, 0, 50)));
    http_response_code($valide ? 200 : 401);
    exit($valide ? 'ok' : 'signature invalide');
}
?><!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta http-equiv="refresh" content="5">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Webhooks reçus · Atelier Pivoine</title>
<style>
body{font-family:system-ui,sans-serif;background:#FBF6EF;color:#1F1F1F;margin:0;padding:2rem 1rem}
h1{color:#2F4A3A;font-family:Georgia,serif;margin:0 0 .25rem}p{margin:0 0 1.5rem;color:#555}
table{border-collapse:collapse;width:100%;max-width:960px;background:#fff;border-radius:12px;overflow:hidden}
th,td{padding:.6rem .8rem;text-align:left;border-bottom:1px solid #eee;font-size:.9rem}
th{background:#2F4A3A;color:#fff}.ok{color:#1b7a3a;font-weight:600}.ko{color:#b3261e;font-weight:600}
</style></head><body>
<h1>Webhooks « commande créée » reçus</h1>
<p>Envoyés par WooCommerce à chaque nouvelle commande · page rafraîchie toutes les 5 s</p>
<?php if (!$recues): ?><p>Aucune commande reçue pour l'instant : passez une commande sur la boutique.</p>
<?php else: ?><table><tr><th>Reçu le</th><th>Événement</th><th>Commande</th><th>Client</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Signature</th></tr>
<?php foreach ($recues as $r): ?><tr>
<td><?= htmlspecialchars($r['recu_le']) ?></td><td><?= htmlspecialchars($r['evenement']) ?></td>
<td>n° <?= (int) $r['numero'] ?></td><td><?= htmlspecialchars($r['client']) ?></td>
<td><?= htmlspecialchars($r['total']) ?></td><td><?= htmlspecialchars($r['paiement']) ?></td><td><?= htmlspecialchars($r['statut']) ?></td>
<td class="<?= $r['signature'] ? 'ok' : 'ko' ?>"><?= $r['signature'] ? 'valide' : 'INVALIDE' ?></td></tr>
<?php endforeach; ?></table><?php endif; ?>
</body></html>

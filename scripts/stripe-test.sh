#!/usr/bin/env bash
# Branche le paiement par carte Stripe en MODE TEST sur la boutique Docker (http://localhost:8080).
# Les clés sont tapées au clavier et vont directement dans la base : jamais dans un fichier, jamais dans Git.
#
#   bash scripts/stripe-test.sh
#
# Clés : dashboard.stripe.com > mode test > Développeurs > Clés API.
set -euo pipefail
export MSYS_NO_PATHCONV=1
cd "$(dirname "$0")/.."
DC="docker compose -f docker-compose.yml"

read -r -p "Clé publiable de test (pk_test_…) : " PK
read -r -s -p "Clé secrète de test (sk_test_…, rien ne s'affiche quand tu tapes ou colles) : " SK; echo
PK=$(echo "$PK" | tr -d '[:space:]'); SK=$(echo "$SK" | tr -d '[:space:]')
case "$PK" in pk_test_*) ;; *) echo "La clé publiable doit commencer par pk_test_ (mode test)." >&2; exit 1 ;; esac
case "$SK" in sk_test_*|rk_test_*) ;; *) echo "La clé secrète doit commencer par sk_test_ (mode test)." >&2; exit 1 ;; esac

$DC run --rm -T -e PK="$PK" -e SK="$SK" cli wp plugin is-active woocommerce-gateway-stripe 2>/dev/null \
  || $DC run --rm -T cli wp plugin install woocommerce-gateway-stripe --version=10.7.2 --activate

$DC run --rm -T -e PK="$PK" -e SK="$SK" cli wp eval '
$o = (array) get_option("woocommerce_stripe_settings", []);
$o["enabled"] = "yes";
$o["testmode"] = "yes";
$o["test_publishable_key"] = getenv("PK");
$o["test_secret_key"] = getenv("SK");
$o["upe_checkout_experience_accepted_payments"] = ["card"];
update_option("woocommerce_stripe_settings", $o);
WC_Stripe_API::set_secret_key(getenv("SK"));
$compte = WC_Stripe_API::request([], "account", "GET");
if (!empty($compte->error)) {
    echo "Clés enregistrées mais refusées par Stripe : " . $compte->error->message . "\n";
    exit(1);
}
echo "Stripe connecté en mode test (compte " . $compte->id . "). Carte de test : 4242 4242 4242 4242, 12/34, 123.\n";
'

#!/usr/bin/env bash
# Reconstruit la boutique à partir du dépôt et d'une sauvegarde, sur http://localhost:8080.
#
#   cp .env.example .env    # puis inventer les 4 valeurs
#   bash scripts/restauration.sh                     → utilise sauvegardes/base-demo.sql + medias-demo.tar.gz
#   bash scripts/restauration.sh 2026-12-05          → utilise base-2026-12-05.sql + medias-2026-12-05.tar.gz
#
# L'adresse du site est changée avec « wp search-replace », jamais à la main dans le fichier SQL :
# WordPress stocke des données sérialisées (avec leur longueur) qui casseraient.
set -euo pipefail
export MSYS_NO_PATHCONV=1                       # Git Bash sous Windows : ne pas réécrire les chemins /…
cd "$(dirname "$0")/.."
DC="docker compose -f docker-compose.yml"
URL="http://localhost:8080"
SUFFIXE="${1:-demo}"
BASE="base-$SUFFIXE.sql"
MEDIAS="medias-$SUFFIXE.tar.gz"

[ -f .env ] || { echo "Fichier .env absent : cp .env.example .env, puis remplir les 4 valeurs." >&2; exit 1; }
[ -f "sauvegardes/$BASE" ] || { echo "Sauvegarde introuvable : sauvegardes/$BASE" >&2; exit 1; }

echo "1/7 Démarrage de MariaDB, WordPress et Mailpit"
$DC up -d db wordpress mailpit
cli() { $DC run --rm -T cli "$@"; }
cli sh -c 'until [ -f wp-config.php ]; do sleep 2; done'   # WordPress a fini de s'installer dans le volume

echo "2/7 Import de la base ($BASE)"
cli wp db import "/sauvegardes/$BASE"

echo "3/7 Changement d'adresse vers $URL"
ANCIENNE=$(cli wp option get siteurl --skip-plugins --skip-themes | tr -d '\r')
if [ "$ANCIENNE" != "$URL" ]; then
  cli wp search-replace "$ANCIENNE" "$URL" --all-tables --precise --skip-plugins --skip-themes
fi

echo "4/7 Extensions (versions compatibles avec WordPress 6.8)"
cli wp plugin install woocommerce --version=10.7.0 --activate --skip-themes
ACTIVES=$(cli wp option get active_plugins --format=json --skip-plugins --skip-themes)
case "$ACTIVES" in *woocommerce-gateway-stripe*)
  cli wp plugin install woocommerce-gateway-stripe --version=10.7.2 --activate --skip-themes ;; esac
case "$ACTIVES" in *updraftplus*)
  cli wp plugin install updraftplus --activate --skip-themes ;; esac

echo "5/7 Site en français"
cli wp language core install fr_FR --activate || true
cli wp language plugin install --all fr_FR || true

echo "6/7 Médias ($MEDIAS)"
if [ -f "sauvegardes/$MEDIAS" ]; then
  cli tar -xzf "/sauvegardes/$MEDIAS" -C wp-content
else
  echo "   (pas d'archive de médias : les images des produits manqueront)"
fi

echo "7/7 Permaliens et caches"
cli wp rewrite flush --hard || true
cli wp transient delete --all || true
cli wp cache flush || true

echo
echo "Boutique restaurée : $URL   (admin : $URL/wp-admin, e-mails : http://localhost:8025)"
echo "Le paiement par carte n'est pas dans la sauvegarde : recoller ses clés de test Stripe dans l'admin."

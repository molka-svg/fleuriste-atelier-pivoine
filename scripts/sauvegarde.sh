#!/usr/bin/env bash
# Sauvegarde de la boutique : la base (produits, commandes, réglages, pages) et les médias.
#
#   bash scripts/sauvegarde.sh          → sauvegardes/base-AAAA-MM-JJ.sql + medias-AAAA-MM-JJ.tar.gz
#   bash scripts/sauvegarde.sh demo     → sauvegardes/base-demo.sql + medias-demo.tar.gz (versionnés dans Git)
#
# En mode « demo », les clés du prestataire de paiement (Stripe…) sont retirées de la base
# avant l'export puis remises : aucun secret ne part dans Git.
# Sur un site Local (Open site shell), les deux commandes du sujet suffisent :
#   wp db export sauvegardes/base-$(date +%F).sql
#   tar -czf sauvegardes/medias-$(date +%F).tar.gz -C wp-content uploads
set -euo pipefail
export MSYS_NO_PATHCONV=1                       # Git Bash sous Windows : ne pas réécrire /sauvegardes
cd "$(dirname "$0")/.."
DC="docker compose -f docker-compose.yml"

SUFFIXE="${1:-$(date +%F)}"
BASE="base-$SUFFIXE.sql"
MEDIAS="medias-$SUFFIXE.tar.gz"
mkdir -p sauvegardes

if [ "${1:-}" = "demo" ]; then
  # Options qui contiennent une clé de paiement : copiées à part, supprimées, exportées sans elles, remises.
  $DC run --rm -T cli sh -c '
    set -e
    noms=$(wp db query "SELECT option_name FROM wp_options WHERE option_value REGEXP \"(sk|pk|rk)_(test|live)_|whsec_\"" --skip-column-names)
    mkdir -p /tmp/cles
    for n in $noms; do wp option get "$n" --format=json > "/tmp/cles/$n.json"; wp option delete "$n" >/dev/null; echo "Secret retiré de l export : $n"; done
    wp db export "/sauvegardes/'"$BASE"'" --add-drop-table
    for n in $noms; do wp option update "$n" --format=json < "/tmp/cles/$n.json" >/dev/null; done
  '
  if grep -qE '(sk|pk|rk)_(test|live)_[A-Za-z0-9]{10,}|whsec_' "sauvegardes/$BASE"; then
    echo "ERREUR : une clé de paiement est encore dans sauvegardes/$BASE, ne pas la committer." >&2
    exit 1
  fi
else
  $DC run --rm -T cli wp db export "/sauvegardes/$BASE" --add-drop-table
fi

$DC run --rm -T cli tar -czf "/sauvegardes/$MEDIAS" -C wp-content uploads
echo "Sauvegarde terminée :"
ls -lh "sauvegardes/$BASE" "sauvegardes/$MEDIAS"

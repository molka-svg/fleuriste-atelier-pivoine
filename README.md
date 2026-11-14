# Atelier Pivoine — boutique en ligne

![Logo](charte/logo.svg)

Fleuriste de quartier fictif, Paris 11e. Équipe : Prénom A, Prénom B, Prénom C, Prénom D.
Commerce, noms, adresses et e-mails **fictifs**.

> 🚧 README en cours de rédaction.

## Où vérifier chaque exigence
| Exigence | Où la voir |
|---|---|
| Charte | charte/CHARTE.md |
| Note de cadrage | NOTE.md |
| Thème exporté | theme/atelier-pivoine |
| Collection Postman | api/boutique.postman_collection.json |
| Restauration | scripts/restauration.sh + capture Docker ci-dessous |

## Captures
Accueil · Fiche produit · Panier · Commande · Vue téléphone (390 px) · Boutique restaurée dans Docker · E-mail Mailpit · Postman

## Relancer la boutique
```bash
git clone https://github.com/<compte>/fleuriste-atelier-pivoine.git
cd fleuriste-atelier-pivoine
cp .env.example .env        # puis remplir les 4 valeurs (mots de passe inventés)
bash scripts/restauration.sh
# ouvrir http://localhost:8080
```

## Choix de design
## Sources des images
| Fichier | Auteur | Source (URL) |
|---|---|---|

## API : en-têtes envoyés et reçus
## Sécurité : 5 mesures et risques couverts
## Sauvegarde : cron, stockage distant, UpdraftPlus
## Déploiement chez un hébergeur cPanel
## Bonus réalisés
## Ce que nous ferions autrement

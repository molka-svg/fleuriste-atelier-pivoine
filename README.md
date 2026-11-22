# Atelier Pivoine — boutique en ligne

![Logo](charte/logo.svg)

Fleuriste de quartier fictif, Paris 11e. Équipe : Molka Ferchichi, Marietou, Prénom C, Prénom D.
Commerce, noms, adresses et e-mails **fictifs**.

## Qui a fait quoi

**Molka Ferchichi**
- Panier détaillé : boutons − / +, barre « livraison offerte dès 60 € », bouton « Continuer mes achats », panier vide, vue téléphone, code promo et bloc total stylisés.
- Commande : formulaire en 3 étapes (coordonnées, livraison ou retrait, bouquet), cartes livraison / retrait, créneau en boutons, récapitulatif avec photos.
- Thème en blocs `theme/atelier-pivoine-blocs` (enfant de Twenty Twenty-Five) : couleurs et polices dans Styles, en-tête, pied de page, accueil en blocs, fiche produit et boutique retouchées, page Mon compte.
- Catalogue `scripts/catalogue.php` : 12 bouquets, 3 catégories, un bouquet en 3 tailles, une promotion datée, une rupture, UGS, galeries, photos Pexels recadrées.
- Code promo BIENVENUE10 (-10 % dès 40 €), boutique en français.
- Captures du README et note de cadrage `NOTE.md`.

**Marietou**
- Thème `theme/atelier-pivoine`, panier latéral, connexion et compte client.
- Docker (`compose.yaml`), script `scripts/configurer-boutique.php`, maquette cliquable du parcours client.

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

| Accueil | Vue téléphone (390 px) |
|---|---|
| ![Page d'accueil](docs/captures/01-accueil.jpg) | ![Accueil sur téléphone, 390 px](docs/captures/05-telephone.jpg) |

**Fiche produit** : « Le bouquet de Camille », produit variable en 3 tailles
![Fiche produit](docs/captures/02-fiche-produit.jpg)

**Panier** : boutons − / +, barre « livraison offerte dès 60 € », code promo
![Panier](docs/captures/03-panier.jpg)

**Commande** : formulaire en 3 étapes, livraison ou retrait, date et créneau, message sur la carte
![Commande](docs/captures/04-commande.jpg)

À venir : boutique restaurée dans Docker · e-mail de commande dans Mailpit · collection Postman

## Relancer la boutique
```bash
git clone https://github.com/molka-svg/fleuriste-atelier-pivoine
cd fleuriste-atelier-pivoine
cp .env.example .env        # puis remplir les 4 valeurs (mots de passe inventés)
bash scripts/restauration.sh
# ouvrir http://localhost:8080
```

## Choix de design
## Sources des images
Photos du thème : voir [theme/atelier-pivoine/images/CREDITS.md](theme/atelier-pivoine/images/CREDITS.md) (Unsplash et Pexels).
Photos des 12 bouquets (dont les 4 ajoutés et les galeries) : voir [theme/atelier-pivoine-blocs/images/CREDITS.md](theme/atelier-pivoine-blocs/images/CREDITS.md).

| Fichier | Auteur | Source (URL) |
|---|---|---|

## API : en-têtes envoyés et reçus
## Sécurité : 5 mesures et risques couverts
## Sauvegarde : cron, stockage distant, UpdraftPlus
## Déploiement chez un hébergeur cPanel
## Bonus réalisés
## Ce que nous ferions autrement

# À faire par le groupe

## Paiement par carte en mode test (Molka)

Demandé dans la partie 4 du sujet : « carte bancaire par un prestataire en mode bac à sable ».
Gratuit : en mode test, aucun argent ne circule.

1. Installer l'extension **WooCommerce Stripe Payment Gateway 10.7.2** (les versions à partir de la 10.8.5 demandent un WooCommerce plus récent que notre 10.7).
2. Créer un compte Stripe et rester en **mode test** : ne pas activer le compte, ne donner aucune information bancaire.
3. Connecté en administrateur : WooCommerce > Réglages > Paiements > Stripe, activer le mode test, coller ses clés `pk_test` et `sk_test`, activer la carte.
4. Payer une commande avec la carte de test `4242 4242 4242 4242`, date future, code `123`.
5. Vérifier la commande « En cours » et l'e-mail dans Mailpit (http://localhost:8083).

**Ne jamais mettre les clés dans Git, ni dans la sauvegarde de démonstration (partie 7).**

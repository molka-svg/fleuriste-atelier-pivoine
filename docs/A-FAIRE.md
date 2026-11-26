# Paiement par carte en mode test (Stripe)

Demandé dans la partie 4 du sujet : « carte bancaire par un prestataire en mode bac à sable ».
Gratuit : en mode test, aucun argent ne circule.

1. Créer un compte sur dashboard.stripe.com et **rester en mode test** : ne pas activer le compte, ne donner aucune information bancaire.
2. Dans Stripe, mode test : **Développeurs > Clés API**, copier la clé publiable `pk_test_…` et la clé secrète `sk_test_…`.
3. Dans le dossier du projet (Git Bash) :
   ```bash
   bash scripts/stripe-test.sh
   ```
   Le script installe l'extension **WooCommerce Stripe Payment Gateway 10.7.2** si besoin (les versions à partir de la 10.8 demandent un WordPress plus récent que notre 6.8), demande les deux clés (la clé secrète ne s'affiche pas), les enregistre dans la base et vérifie auprès de Stripe qu'elles fonctionnent : `Stripe connecté en mode test (compte acct_…)`.
4. Payer une commande avec la carte de test `4242 4242 4242 4242`, date future (`12/34`), code `123`. La carte `4000 0000 0000 0002` montre un paiement refusé.
5. Vérifier la commande « En cours » dans WooCommerce > Commandes, l'e-mail dans Mailpit (http://localhost:8025) et le paiement dans Stripe > Paiements.

Pourquoi un script plutôt que l'admin : la version 10.7.2 ne propose plus de saisir les clés à la main, seulement un formulaire « Créer ou connecter un compte » qui demande de vraies coordonnées bancaires.

**Ne jamais mettre les clés dans Git.** `scripts/sauvegarde.sh demo` les retire de la sauvegarde de démonstration et refuse de continuer s'il en trouve une ; après une restauration, relancer `bash scripts/stripe-test.sh`.

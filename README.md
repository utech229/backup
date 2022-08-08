# Script (sauvegarde base de donnée MySQL, Compression GZIP, upload sur Google Drive)

## Configurer le script avec les identifiants pour la base de données et l'application

---

$applicationName = "";
$dbname = "";
$dbuser = "";
$dbpass = "";
$dbhost = "";

---

## Sendmail requis dans le php.ini pour l'envoi d'email.

## Changer l'url de redirection en fonction : $REDIRECT_URI

## ENSUITE
- Pour permettre l'execution d'un script shell à partir de php il faut
enlever la fonction shell_exec des fonctions blacklistés dans le fichier php.ini
(si ell est blacklistée)
- Configurer la tache cron

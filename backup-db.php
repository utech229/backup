<?php
$applicationName = "zekin";
$dbname = "sample";
$dbuser = "khaled";
$dbpass = "festidereves";
$dbhost = "localhost";

$dbexport = $applicationName . "__" . date("Y-m-d-H-i-s") . ".sql.gz";

// Création du fichier dump de sauvegarde
$command = "mysqldump --opt --host=$dbhost --password=$dbpass --user=$dbuser --databases $dbname |  gzip > $dbexport";
exec($command, $output, $worked);

switch ($worked) {
  case 0:
    echo "La base de données <b>$dbname</b> à été stocké avec succès dans le chemin suivant: <b>" . getcwd() . "/$dbexport</b>";
    break;
    case 1:
    echo "Un erreur est survenue lors de l'exportation de <b>$dbname</b> vers: <b>" . getcwd() . "/$dbexport</b>";
    break;
  case 2:
    echo "Un erreur est survenue lors de l'exportation, veuillez verifier les informations passées en paramètre.";
    break;
  }


// Lancer la requête curl
$OAUTH2_TOKEN_URI = 'https://oauth2.googleapis.com/token'; 
$DRIVE_FILE_UPLOAD_URI = 'https://www.googleapis.com/upload/drive/v3/files'; 
$DRIVE_FILE_META_URI = 'https://www.googleapis.com/drive/v3/files/'; 

$GOOGLE_CLIENT_ID = "779702624699-b9o70ak74udskikfqvneba8rcm5gv9m7.apps.googleusercontent.com";
$GOOGLE_CLIENT_SECRET = "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";
$GOOGLE_OAUTH_SCOPE = "https://www.googleapis.com/auth/drive";
$REDIRECT_URI = "http://localhost/bakkup/redirected.php";
$CODE = "4/0AdQt8qga4_Cnhgc1_jrye2hT7LFfYX2euQ2im0UmPC-Ik-Set_Y0VDS_5eVJLag3EQyq8A";
$REFRESH_TOKEN = "1//04isqpDHxl98LCgYIARAAGAQSNwF-L9IrmlxGdAtEeXaE-WfDnU6uISMFseJ7TYtDbKPGNED4WQdm3Tjz_r2Pzus2monmIwk_eV0";

try {
  $targetPath = $dbexport;
  $target_file = $targetPath;
  $file_content = file_get_contents($target_file);
  $mime_type = mime_content_type($target_file);
  $data = getAccessToken($GOOGLE_CLIENT_ID, $REFRESH_TOKEN, $GOOGLE_CLIENT_SECRET);
  $access_token = $data['access_token'];
  echo shell_exec("sh ./uploadToDrive.sh $access_token $target_file");
} catch (Exception $e) {
  echo "Erreur survenue lors de l'upload sur GD: " . $e->getMessage();
}

// Functions
function getAccessToken($GOOGLE_CLIENT_ID, $REFRESH_TOKEN, $GOOGLE_CLIENT_SECRET) {
  $OAUTH2_TOKEN_URI = 'https://oauth2.googleapis.com/token'; 
  $curlPost = "client_id=$GOOGLE_CLIENT_ID&client_secret=$GOOGLE_CLIENT_SECRET&refresh_token=$REFRESH_TOKEN&grant_type=refresh_token"; 
  $ch = curl_init();         
  curl_setopt($ch, CURLOPT_URL, $OAUTH2_TOKEN_URI);         
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
  curl_setopt($ch, CURLOPT_POST, 1);         
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);     
  $data = json_decode(curl_exec($ch), true); 
  $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
   
  if ($http_code != 200) { 
      $error_msg = 'Echec lors de la reception du token'; 
      if (curl_errno($ch)) { 
          $error_msg = curl_error($ch); 
      } 
      throw new Exception('ErreurHTTP '.$http_code.': '.$error_msg); 
  } 
       
  return $data; 
}



// todo 
// - Ajouter les metadonnées du fichier: nom, dossier de transfert FAIT
// - Refactoriser le nom du fichier sql.gz zip générer FAIT
// - Configurer la tâche cron.
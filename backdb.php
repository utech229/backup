<?php
// Configurations
$applicationName = "";
$dbname = "";
$dbuser = "";
$dbpass = "";
$dbhost = "";

$dbexport = $applicationName . "__" . date("Y-m-d-H-i-s") . ".sql.gz";

// Création du fichier dump de sauvegarde
// Envoi email: début de sauvegarde
sendEmail("Démarrage de la sauvegarde: $applicationName le ". date("Y-m-d") . "" . date("H-i-s"), $applicationName);

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
$OAUTH2_TOKEN_URI = ''; 
$DRIVE_FILE_UPLOAD_URI = ''; 
$DRIVE_FILE_META_URI = ''; 
$API_KEY = '';
$GOOGLE_CLIENT_ID = "";
$GOOGLE_CLIENT_SECRET = "";
$GOOGLE_OAUTH_SCOPE = "";
$REDIRECT_URI = "";
$CODE = "";
$REFRESH_TOKEN = "";

try {
  $targetPath = $dbexport;
  $target_file = $targetPath;
  $file_content = file_get_contents($target_file);
  $mime_type = mime_content_type($target_file);
  $data = getAccessToken($GOOGLE_CLIENT_ID, $REFRESH_TOKEN, $GOOGLE_CLIENT_SECRET);
  $access_token = $data['access_token'];
  // Get the oldest backup drive file ID
  $res = getOldestDriveBackupId($applicationName, $API_KEY, $access_token);
  // If the drive folder contains a number of backup > 3, delete the oldest
  if($res['delete']) {
    // Delete A specific drive file
    deleteDriveFile($res['id'], $API_KEY, $access_token);
    echo "Deleted this file -> ". $res['id'];
    // Upload the file to drive
    echo shell_exec("sh ./uploadToDrive.sh $access_token $target_file");
    // Envoi email: FIN de sauvegarde
    sendEmail("Fin de la sauvegarde: $applicationName le ". date("Y-m-d") . "" . date("H-i-s"), $applicationName);
    exit();
  } 
  // Upload the file to drive
  echo shell_exec("sh ./uploadToDrive.sh $access_token $target_file");
  // Envoi email: FIN de sauvegarde
  sendEmail("Fin de la sauvegarde: $applicationName le ". date("Y-m-d") . "" . date("H-i-s"), $applicationName);

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

function sendEmail($message, $applicationName) {
  $to_email = "urbantech229@gmail.com";
  $subject = "Sauvegarde de la base de donnée: $applicationName";
  $body = $message;
  $headers = "From: DB Backup";

  if (mail($to_email, $subject, $body, $headers)) {
    echo "Email envoyé avec succès à: $to_email... \n";
  } else {
    echo "Erreur durant l'envoi de l'email. \n";
  }
}

function deleteDriveFile($fileId , $API_KEY, $access_token) {
  echo shell_exec("sh ./deleteDriveFile.sh $fileId $API_KEY $access_token");
}

function getOldestDriveBackupId($applicationName, $API_KEY, $access_token) {
  $res = shell_exec("sh ./getOldestDriveBackupId.sh $applicationName $API_KEY $access_token");
  $resToAssoc = json_decode($res, true);
  if(count($resToAssoc['files']) >= 3) {
    return ['delete' => true, 'id' => $resToAssoc['files'][0]['id']];
  }
  return ['delete' => false, 'id' => null];    
}
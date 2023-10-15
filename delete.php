<?php

require_once __DIR__ . '/vendor/autoload.php';

include_once "templates/base.php";

function deleteFromGoogleDrive($deleteFileId, $authCode)
{
    $client = new Google\Client();

    if ($credentials_file = checkServiceAccountCredentialsFile()) {
        // set the location manually
        $client->setAuthConfig($credentials_file);
    } elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
        // use the application default credentials
        $client->useApplicationDefaultCredentials();
    } else {
        echo missingServiceAccountDetailsWarning();
        return;
    }
    
    try {
        $client->addScope(Google\Service\Drive::DRIVE);
        $driveService = new Google\Service\Drive($client);
        $deleteFileIds = array();
        array_push($deleteFileIds, $deleteFileId);
        foreach ($deleteFileIds as $id) {
            $driveService->files->delete($id);
        }
    } catch(Exception $e) {
       echo "Error Message: ".$e;
    }

    header('Location: /?id='.ID.'&action=top&auth_code='.$authCode.'#uploaded-contents');
}


// get delete file id
$deleteFileId = $getPost['delete_file_id'];

// check auth
if ($authType != USER_TYPE_ADMIN) {
    header('Location: /?id='.ID.'&action=top');
} else {
    deleteFromGoogleDrive($deleteFileId, $getParam['auth_code']);
}

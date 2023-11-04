<?php

require_once __DIR__ . '/vendor/autoload.php';

include_once "templates/base.php";

function deleteAllFromGoogleDrive($authCode)
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
        $files = array();
        $deleteFileIds = array();
        $pageToken = null;
        do {
            $response = $driveService->files->listFiles(array(
                'q' => "mimeType='image/png' 
                        or mimeType='image/jpeg'
                        or mimeType='video/mp4'
                        or mimeType='video/quicktime'",
                'fields' => 'files(id, name, fileExtension)',
            ));
            foreach ($response->files as $file) {
                array_push($deleteFileIds, $file->id);
            }
            array_push($files, $response->files);

            $pageToken = $response->pageToken;
        } while ($pageToken != null);

        foreach ($deleteFileIds as $id) {
            echo $id;
            $driveService->files->delete($id);
        }
    } catch(Exception $e) {
       echo "Error Message: ".$e;
    }

    header('Location: /?id='.ID.'&action=top&auth_code='.$authCode.'#uploaded-contents');
}


// check auth
if ($authType != USER_TYPE_ADMIN) {
    header('Location: /?id='.ID.'&action=top');
} else {
    deleteAllFromGoogleDrive($getParam['auth_code']);
}

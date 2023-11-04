<?php

require_once __DIR__ . '/vendor/autoload.php';

include_once "templates/base.php";

function uploadToGoogleDrive($authCode, $filePath, $folderName)
{
    printf("=============================================<br />");
    printf("Start Script:%s<br />", date("Y-m-d h:m:s"));
    printf("Save FilePath To Google Drive=>%s<br />", $filePath);
    printf("Save Folder In Google Drive=>%s<br />", $folderName);
    printf("<br />");
    
    if (!file_exists($filePath))
    {
        echo "$filepath is not exists";
        die("$filepath is not exists");
        exit();
    }
    
    $path_parts = pathinfo($filePath);
    $fileName = $path_parts['basename'];
    
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
    $drive = new Google\Service\Drive($client);


    try {
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google\Service\Drive::DRIVE);
        $driveService = new Google\Service\Drive($client);
        $fileMetadata = new Google\Service\Drive\DriveFile(array(
            'name' => $fileName,
            'parents' => array(DRIVE_ID),
        ));
        $mime = mime_content_type($filePath);
        // $content = file_get_contents('./nightsky.png');
        $content = file_get_contents($filePath);
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mime,
            'uploadType' => 'multipart',
            'fields' => 'id'));
        printf("File ID: %s\n", $file->id);
        return $file->id;
    } catch(Exception $e) {
        echo "Error Message: ".$e;
    } 
    
}


// if file name is none, to top
if (empty($uploadFiles['file']['name'][0])) {
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit;
}

$uploadDir = './';

for ($i=0; $i<count($uploadFiles['file']['name']); ++$i) {

    $tmpPath = $uploadFiles['file']['tmp_name'][$i];

    $uploadFile = $uploadDir . basename($uploadFiles['file']['name'][$i]);

    $fileSizeWithMB = filesize($tmpPath) / 1024 / 1024;

    if ($fileSizeWithMB > MAX_UPLOAD_SIZE_MB_PER_FILE) {
        header('Location: /?id='.ID.'&action=top&auth_code='.$authCode);
        exit;
    }

    if (move_uploaded_file($uploadFiles['file']['tmp_name'][$i], $uploadFile)) {
        echo "File is valid, and was successfully uploaded.\n";

        uploadToGoogleDrive($authCode, $uploadFile, "p-share-test");

        unlink($uploadFile);

    } else {
        echo "Possible file upload attack!\n";
        exit;
    }

}


header('Location: /?id='.ID.'&action=top&auth_code='.$authCode);
exit;

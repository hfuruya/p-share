<?php

require_once __DIR__ . '/vendor/autoload.php';

include_once "templates/base.php";

?>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link href="css/style.css?v=<?php echo VERSION ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

    <title>PhotoShare (β) | Only Private Photo Share Service</title>

</head>

<script>
    const MAX_UPLOAD_FILE_NUM = <?php echo MAX_UPLOAD_FILE_NUM ?>;
    const MAX_UPLOAD_SIZE_MB_PER_FILE = <?php echo MAX_UPLOAD_SIZE_MB_PER_FILE ?>;

    function checkFileNum () {
        let fileList = document.getElementById("select-file").files;
        if (fileList.length > MAX_UPLOAD_FILE_NUM) {
            alert("limit file num "+MAX_UPLOAD_FILE_NUM+" per 1 upload !!");
            location.href = "";
        }
    }

    $(function(){
        $('#max_upload_file_num').html(MAX_UPLOAD_FILE_NUM);
        $('#max_upload_size_mb_per_file').html(MAX_UPLOAD_SIZE_MB_PER_FILE);

        let btnSubmitClickFlg = false;
        $('#submit-upload').click(function() {
            if (btnSubmitClickFlg) return;
            btnSubmitClickFlg = true;
            $('#loading').css('display', 'block');
            $('#loading').addClass('loaded');
            $('html').css('overflow', 'hidden');
        });

        $('.delete_file').click(function() {
            if (btnSubmitClickFlg) return;
            btnSubmitClickFlg = true;
            $('#loading').css('display', 'block');
            $('#loading').addClass('loaded');
            $('html').css('overflow', 'hidden');
        });

        document.body.addEventListener("touchstart", function(e){
            if (e.touches && e.touches.length > 1) {
                e.preventDefault();
            }
        }, {passive: false});
        document.body.addEventListener("touchmove", function(e){
            if (e.touches && e.touches.length > 1) {
                e.preventDefault();
            }
        }, {passive: false});

        $('#uploaded-contents').css('display', 'flex');
        $('#uploaded-contents').css('flex-wrap', 'wrap');
    });

</script>

<div id="loading" style="display: none;">
  <div class="spinner"></div>
</div>

<header id="header">
    <h1 class="site-title"><a href="">PhotoShare (β)</a></h1>
</header>

<div class="wrapper">

    <div id="desc-parts">
        This is Photo Share Service. <br />
        Please share your photos here.
    </div>

    <div id="upload-parts">
        Select Upload Files<br />
        <br />
        ※to be uploaded to ●●'s google drive<br />
        ※multi file select. only .png or .jpg <br />
        ※limit file num per 1 upload <span id="max_upload_file_num"></span><br />
        ※max size per file <span id="max_upload_size_mb_per_file"></span> MB<br />
        <br />
        <form method="POST" action="/?id=<?php echo $getParam['id'] ?>&action=upload&auth_code=<?php echo $authCode ?>&p=1" enctype="multipart/form-data">
            <div class="original-file-btn">
                Select
                <input type="file" id="select-file" class="btn btn-light" name="file[]" accept=".png, .jpg, .jpeg, .mov" onchange="checkFileNum();" multiple /><br />
            </div>
            <br />
            <input type="submit" id="submit-upload" class="upload-file-btn btn btn-primary" value="Upload"/>
        </form>
    </div>

    <div class="hr"></div>

    <div>
        <div id="disp-parts">
            Here you will see a list of images that you have uploaded.<br />
            <br />
            ◆How to download<br />
            　Smartphone：Please press and hold the image.<br />
            　PC：Please right click.<br />
            　※From <a href="https://drive.google.com/drive/folders/<?php echo DRIVE_ID ?>?usp=drive_link" target="_blank">Here</a>, you can full download.
        </div>

        <div id="uploaded-contents">
<?php


function getFromGoogleDrive($paramId, $authType, $paramAuthCode, $currentPage)
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
        $pageToken = null;
        do {
            $response = $driveService->files->listFiles(array(
                'q' => "mimeType='image/png' 
                        or mimeType='image/jpeg'
                        or mimeType='image/heic'",
                        // or mimeType='video/mp4'
                        // or mimeType='video/quicktime'",
                'fields' => 'files(id, name, fileExtension)',
            ));
            $files = $response->files;
            $filesNum = count($files);
            $startOffset = PER_PAGE * ($currentPage - 1);
            $pageOffset = $startOffset+PER_PAGE-1 < $filesNum ? PER_PAGE : round($filesNum % PER_PAGE);
            for ($i=$startOffset; $i<$startOffset+$pageOffset; $i++) {
                if (empty($files[$i])) {
                    return;
                }
                $ext = strtolower($files[$i]['fileExtension']);
                printf("<div>");
                switch ($ext) {
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                    case 'heic':
                        printf("<img src='https://drive.google.com/uc?id={$files[$i]['id']}' width='100px'>");
                        break;
                    case 'mov':
                    case 'mp4':
                        printf("<iframe src='https://drive.google.com/file/d/{$files[$i]['id']}/preview' width='100px' allow='autoplay'></iframe>");
                        break;
                }

                // only admin
                if ($authType == USER_TYPE_ADMIN) {
                ?>
                <form method="POST" action="/?id=<?php echo $paramId ?>&action=delete&auth_code=<?php echo $paramAuthCode ?>&p=<?php echo $currentPage ?>">
                    <input type="hidden" name="delete_file_id" value="<?php echo $files[$i]['id'] ?>" /><br />
                    <input type="submit" class="delete_file delete-file-btn btn btn-danger" value="×"/>
                </form>
                <?php
                }
                printf("</div>");
            }
            $pageToken = $response->pageToken;
        } while ($pageToken != null);
    } catch(Exception $e) {
       header('Location: /?id='.ID.'&action=top&auth_code='.$paramAuthCode);
    }

?>

    </div>

<?php

    $maxPageNum = 1;
    if ($filesNum % PER_PAGE == 0) {
        $maxPageNum = round($filesNum / PER_PAGE);
    } else {
        if ($filesNum > PER_PAGE) {
            $maxPageNum = round($filesNum / PER_PAGE) + 1;
        }
    }
?>
    <div class="mb10">
        <?php echo $startOffset+1 ?>
            <?php
                if ($pageOffset != 1) {
                    ?>
                    〜 <?php echo $startOffset+$pageOffset ?>
                    <?php
                }
            ?>
    </div>

    <div class="mb10">
<?php
    for ($i=0; $i<$maxPageNum; $i++) {
        if ($i+1 == $currentPage) {
            ?>
            <span style="font-weight: bold;"><?php echo ($i+1) ?></span>&emsp;
            <?php
        } else {
            ?>
            <a href="/?id=<?php echo $paramId ?>&action=top&auth_code=<?php echo $paramAuthCode ?>&p=<?php echo ($i+1) ?>#uploaded-contents"><?php echo ($i+1) ?></a>&emsp;
            <?php
        }
    }
    ?>
    </div>
<?php
}

getFromGoogleDrive($getParam['id'], $authType, $getParam['auth_code'], $currentPage);

?>

</div>


<?php

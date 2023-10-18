# Photo Share

# Overview

This repository is for testing the Google Drive API.


## Description

This is the Test Service.

With Google Drive API, you can share photos, movies, and more.

Only one service account owns their files, so it doesn't take up space on the person uploading it.


# Demo

![Image](https://raw.githubusercontent.com/hfuruya/p-share/main/demo-image.gif)

## Requirement

PHP ^ 8.1

（ Local Dev Env : RockyLinux 9 on Docker Desktop ^ 4.19.0 ）


## Usage


### Example : config.php

```config.php
// ID
define("ID", "your_event_id");

// drive id
define("DRIVE_ID", "your_drive_id");

// limit upload num
define("MAX_UPLOAD_FILE_NUM", "5");

// limit upload file size(MB) per file
define("MAX_UPLOAD_SIZE_MB_PER_FILE", "50");

// wait time when uploading
define("MAX_WAIT_SECONDS", "10");

// radomize auth code
define("AUTH_CODE_ADMIN", "your_auth_code");

// user type
define("USER_TYPE_GENERAL", 0);
define("USER_TYPE_ADMIN", 1);

// limit display num per page
define("PER_PAGE", 10);

// version
define("VERSION", "202310150000");
```


## Install

```
$ git clone this repository

$ php composer install
```

Please configure OAuth by referring to here.

* [Using OAuth 2.0 for Web Server Applications - google-api-php-client](https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md)

* [Google Drive API overview](https://developers.google.com/drive/api/guides/about-sdk)

### Only Local Dev

```
$ docker-compose -p p-share up -d

$ docker exec -u 0 -it web bash -c "composer install"
```

You can access to 

* General : http://localhost:9081/?id=your_event_id&action=top

* Administrator : http://localhost:9081/?id=your_event_id&action=top&auth_code=your_auth_code
    * You can delete uploaded files.

# Author

* [@h_furuya_](https://x.com/h_furuya_)

# License

[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/mit-license.php)

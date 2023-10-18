<?php

// GET param
parse_str($_SERVER['QUERY_STRING'], $getParam);

// POST param
$getPost = $_POST;

// FILES param
$uploadFiles = $_FILES;

// auth
$authCode = "";
$authType = USER_TYPE_GENERAL;
if (!empty($getParam['auth_code'])) {
    $authCode = $getParam['auth_code'];
    $authType = $authCode === AUTH_CODE_ADMIN ? USER_TYPE_ADMIN : USER_TYPE_GENERAL;    
} else {
    $getParam['auth_code'] = $authCode;
}

// current page
$currentPage = !empty($getParam['p']) ? $getParam['p'] : 1;

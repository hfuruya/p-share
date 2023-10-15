<?php

if (!array_key_exists('id', $getParam) || $getParam['id'] != ID) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

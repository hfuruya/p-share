<?php

if ($getParam['action'] == 'upload')
{
    include_once "upload.php";
}
elseif ($getParam['action'] == 'delete')
{
    include_once "delete.php";
}
else
{
    include_once "top.php";
}

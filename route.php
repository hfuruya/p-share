<?php

if ($getParam['action'] == 'upload')
{
    include_once "upload.php";
}
elseif ($getParam['action'] == 'delete')
{
    include_once "delete.php";
}
elseif ($getParam['action'] == 'delete_all')
{
    include_once "delete_all.php";
}
else
{
    include_once "top.php";
}

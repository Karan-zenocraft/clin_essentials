<?php

//DEFINE CONSTANT FOR DIRECTORY SEPRATOR
defined("DS") or define("DS", DIRECTORY_SEPARATOR);
// DEFINE CONSTANT FOR INDEX FILE NAME OF CREATED DIRECTORY
defined("INDEX_FILE") or define("INDEX_FILE", "index.html");
//CREATE NECESSARY DIRECTORIES
$ssBasePath = __DIR__;
define("BASEPATH",$ssBasePath);
// DEFINE CONSTANT FOR UPLOAD PATH
defined("UPLOAD_PATH") or define("UPLOAD_PATH", $ssBasePath . DS . "uploads" . DS);
if (!is_dir(UPLOAD_PATH))
{
    //CREATE UPLOAD DIR
    mkdir(UPLOAD_PATH);
    chmod(UPLOAD_PATH, 0777);
    //CREATE INDEX FILE SO END USER CANNOT SEE THE CONTENT OF DIRECTORY
    fopen(UPLOAD_PATH . INDEX_FILE, "w");
}
$amRequiredDir = ["profile_pictures", "team_images"];
if (!empty($amRequiredDir))
{
    foreach ($amRequiredDir as $ssDirName)
    {
        $ssDirFullPath = UPLOAD_PATH . $ssDirName;
        if (!is_dir($ssDirFullPath))
        {
            mkdir($ssDirFullPath);
            //CHANGE PERMISSION
            chmod($ssDirFullPath, 0777);
            //CREATE INDEX FILE SO END USER CANNOT SEE THE CONTENT OF DIRECTORY
            fopen($ssDirFullPath . DS . INDEX_FILE, "w");
        }
    }
}

<?php

    //For debugging
    error_reporting(E_ALL);
    ini_set("display_errors", "On");

    //setting the default timezone
    date_default_timezone_set('Asia/Kolkata');

    //working directory
    define('CURR_WORKING_DIR', dirname(__FILE__) . "/");

    // Include the SDK using the Composer autoloader
    require_once CURR_WORKING_DIR . 'vendor/autoload.php';
    require_once CURR_WORKING_DIR . 'helpers.php';

    //accessing config
    $config = getDbConfig();

    //setting the db config
    $host = $config->HOST;
    $user = $config->USER;
    $password = base64_decode($config->PASSWORD);
    $userDbName = $config->USER_DATABASE;
    $authDbName = $config->AUTH_DATABASE;
    $rootDirectory = $config->BACKUP_ROOT_DIRECTORY;
    $bucket = $config->S3_BUCKET;

    //clean the root directory
    deleteDirFiles($rootDirectory);

    try{

        //taking the sql dump for mobilebackend
        $output = NULL;
        $commandSqlDump = 'mysqldump -u'.$user.' -p'.$password.' '. $userDbName .' > '.$rootDirectory.'"'. $userDbName .'-"`date +"%d-%m-%Y"`.sql';
        echo "\n" . exec($commandSqlDump, $output);
        $commandGzipDump = 'gzip '.$rootDirectory.'"'. $userDbName .'-"`date +"%d-%m-%Y"`.sql';
        echo "\n" . exec($commandGzipDump, $output);
        echo "\n DB Backup Complete :- " . $userDbName;

        //taking the sql dump for mobilebackend	sentinel
        $output = NULL;
        $commandSqlDump = 'mysqldump -u'.$user.' -p'.$password.' '. $authDbName .' > '.$rootDirectory.'"'. $authDbName.'-"`date +"%d-%m-%Y"`.sql';
        echo "\n" . exec($commandSqlDump, $output);
        $commandGzipDump = 'gzip '.$rootDirectory.'"'. $authDbName .'-"`date +"%d-%m-%Y"`.sql';
        echo "\n" . exec($commandGzipDump, $output);
        echo "\n DB Backup Complete :- " . $authDbName;

        echo "\n\n Directory Name :- " . $rootDirectory . " \n\n";
        //iterating through the contents of a app database folder
        $iterator = new DirectoryIterator($rootDirectory);

        foreach ( $iterator as $fileInfo ) {

            //excluding the . and .. directories
            if(!$fileInfo->isDot()) {

                //fetching the file names and file paths
                $fileName = $fileInfo->current()->getFilename();
                $filePath = $fileInfo->current()->getPathName();

                $virtualFilePath = "daily-backups/" . $fileName;

                $result = "FileInfo";
                echo "\n\n File Name :- " . $fileName . "\n\n";
                echo "\n\n File Path :- " . $filePath . "\n\n";
                echo "\n\n File Extension :- " . $fileInfo->getExtension() . "\n\n";

                //Uploading the files
                $result = uploadFileToS3($bucket, $virtualFilePath, $filePath);

                //logging the upload result into the log file
                echo "\n";
                $logMessage = "--Upload Output--" . PHP_EOL . json_encode($result);
                echo "\n";
                echo $logMessage;
                echo "File :" . $fileName . " has been uploaded.";
                echo "\n";
            }
        }
    } catch (Exception $e) {

        echo "\n";
        //displaying the errors
        echo "Exception :- " . $e->getMessage();
        echo "\n";

        //logging the upload result into the log file
        echo "\n";
        $logMessage = date("l jS \of F Y h:i:s A") . PHP_EOL . json_encode($e->getMessage());
        echo $logMessage;
        echo "\n";
    }


?>


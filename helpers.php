<?php

//function to fetch db config
function getDbConfig() {

    $DB_CONFIG = file_get_contents(CURR_WORKING_DIR . "config.json");
    $DB_CONFIG = json_decode($DB_CONFIG);
    return $DB_CONFIG;
}

//method to instantiate an s3 client
function getS3Client() {

    //fetching config
    $CONFIG = getDBConfig();

    $s3 = new Aws\S3\S3Client([
        'credentials' => array(
            'key'    => $CONFIG->AWS_S3_BUCKET_API_KEY,
            'secret' => $CONFIG->AWS_S3_BUCKET_API_SECRET,
        ),
        'version' => 'latest',
        'region'  => 'ap-southeast-1'
    ]);

    return $s3;
}

//method to upload a file
function uploadFileToS3($bucket, $virtualFilePath, $sourceUrl) {

    //setting up the aws client
    $client = getS3Client();

    //Upload an object to Amazon S3
    $result = $client->putObject(array(
        'Bucket' => $bucket,
        'Key'    => $virtualFilePath,
        'SourceFile' => $sourceUrl
    ));

    //returning the response after upload
    return $result;
}

class PDOConnectionFactory{

    // receives the connection
    public $con = null;

    // switch database?
    public $dbType 	= "mysql";

    // connection parameters
    // when it will not be necessary leaves blank only with the double quotations marks ""
    public $host 	= "localhost";
    public $user 	= "root";
    public $password 	= "test";
    public $database	= "test";

    // arrow the persistence of the connection
    public $persistent = false;

    // new PDOConnectionFactory( true ) <--- persistent connection
    // new PDOConnectionFactory()       <--- no persistent connection
    public function __construct($config, $persistent = false){

        // it verifies the persistence of the connection
        if( $persistent != false){ $this->persistent = true; }

        //setting the db config
        $this->host = $config->host;
        $this->user = $config->user;
        $this->password = $config->password;
        $this->database = $config->database;

    }

    public function getConnection(){
        try{
            // it carries through the connection
            $this->con = new PDO($this->dbType.":host=".$this->host.";dbname=".$this->database, $this->user, $this->password,
                array( PDO::ATTR_PERSISTENT => $this->persistent ) );
            // carried through successfully, it returns connected
            return $this->con;
            // in case that an error occurs, it returns the error;
        }catch ( PDOException $ex ){  echo "Error: ".$ex->getMessage(); }

    }

    // close connection
    public function Close(){
        if( $this->con != null )
            $this->con = null;
    }

}

function deleteDirFiles($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            rmdir($file);
            echo "\n Deleted Directory :- " . $file . "\n";
        } else {
            unlink($file);
            echo "\n Deleted File :- " . $file . "\n";
        }
    }
}

?>
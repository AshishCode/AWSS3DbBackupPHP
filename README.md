# AWSS3DbBackupPHP
Simple script to upload database backups to AWS S3 Buckets

Steps -

 composer install

 vim config.json
 
{
  "AWS_S3_BUCKET_API_KEY": "_ANY_KEY_",
  "AWS_S3_BUCKET_API_SECRET": "_ANY_SECRET_",
  "HOST":"58.71.290.57:3306",
  "USER":"root",
  "PASSWORD":"_ANY_PASSWORD_",
  "AUTH_DATABASE":"_ANY_DB_",
  "USER_DATABASE":"_ANY_DB_",
  "BACKUP_ROOT_DIRECTORY":"/var/www/html/_ANY_PATH_/",
  "S3_BUCKET":"_ANY_S3_BUCKET_"
}

 php backup.php

Note. Ensure you have read and write permissions on the `BACKUP_ROOT_DIRECTORY`

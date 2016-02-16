<?php
$DefaultLanguage ='en_GB.utf8';
$allow_demo_mode = False;
$host = 'localhost';
$dbType = 'mysql';
$dbuser = 'root';
$dbpassword = '';
putenv('TZ=Africa/Nairobi');
$AllowCompanySelectionBox = true;
$DefaultCompany = 'airads';
$SessionLifeTime = 120;
$MaximumExecutionTime =30000;
$CryptFunction = 'sha1';
$DefaultClock = 12;
$rootpath = dirname($_SERVER['PHP_SELF']);
if (isset($DirectoryLevelsDeep)){
   for ($i=0;$i<$DirectoryLevelsDeep;$i++){
$rootpath = substr($rootpath,0, strrpos($rootpath,'/'));
} }
if ($rootpath == '/' OR $rootpath == '\\') {;
$rootpath = '';
}
error_reporting (E_ALL & ~E_NOTICE);
?>
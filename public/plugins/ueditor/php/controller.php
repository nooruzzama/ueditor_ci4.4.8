<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
//exit();//this function i have moved in application//ThirdParty/.....
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* Pictures */
    case 'uploadimage':
    /* Upload graffiti */
    case 'uploadscrawl':
    /* upload video */
    case 'uploadvideo':
    /* Upload files */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* List images */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* list files */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* Crawling remote files */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> 'Request address error'
        ));
        break;
}

/* Output results */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback Illegal parameters'
        ));
    }
} else {
    echo $result;
}
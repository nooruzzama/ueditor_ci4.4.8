<?php
/**
 * Upload Configuration
 *
 * @package             UEditor Config
 * @author              Noor <noor-uzzama@outlook.com>
 * @version             V1.0
 * @copyright           Copyright 2024 UEditor CI4 Developed By Md Nooruzzama
 * @modifier            Noor <noor-uzzama@outlook.com>
 * @created             2024-06-29  
 */
namespace Config;

use CodeIgniter\Config\BaseConfig;

class UEditorConfig extends BaseConfig
{
    
    /*
     * Upload parameters
     */

    public array $upload_params = [
        'allowed_types' => 'gif|jpg|png|jpeg',
        'max_size' => '5000',
        'remove_spaces' => true,
        'encrypt_name' => true,
        'upload_path' => ROOTPATH.'/public/',//Upload Path
        'pic_path'=>'/'//Image path for display
    ];

    //百度编辑器文件上传配置
    public $ueditor_config = '{
        /* Upload image configuration items */
        "imageActionName": "uploadimage", /*  The name of the action to upload the image  */
        "imageFieldName": "upfile", /* Submitted picture form name */
        "imageMaxSize": 2048000, /* upload size limit, unit B */
        "imageAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* Upload image format display  */
        "imageCompressEnable": true, /* whether to compress the image, the default is true */
        "imageCompressBorder": 1600, /* Image compression longest side limit */
        "imageInsertAlign": "none", /* The inserted image floats  */
        "imageUrlPrefix": "http://ueditorci4.noor", /* Image access path prefix */
        "imagePathFormat": "/uploads/editors/image/{yyyy}{mm}{dd}/{time}", /* Upload the save path, you can customize the save path and file name format */
                                    /* {filename} It will be replaced with the original file name. When configuring this item, attention should be paid to the issue of Chinese garbled characters */
                                    /* {rand:6} It will be replaced with a random number, and the following numbers are the digits of the random number */
                                    /* {time} Will be replaced with a timestamp */
                                    /* {yyyy} It will be replaced with a four digit year */
                                    /* {yy} It will be replaced with two digit years */
                                    /* {mm} It will be replaced with two months */
                                    /* {dd} It will be replaced with two dates */
                                    /* {hh} It will be replaced with two hours*/
                                    /* {ii} It will be replaced with two minutes*/
                                    /* {ss} It will be replaced with two seconds */
                                    /* Illegal characters \ : * ? " < > | */
                                    /* Please have a look at the online document: fex.baidu.com/ueditor/#use-format_upload_filename */

        /* Graffiti image upload configuration item */
        "scrawlActionName": "uploadscrawl", /* The action name for executing the upload graffiti */
        "scrawlFieldName": "upfile", /* The name of the submitted image form */
        "scrawlPathFormat": "/uploads/editors/image/{yyyy}{mm}{dd}/{time}", /* Upload save path, customizable save path and file name format*/
        "scrawlMaxSize": 2048000, /* Upload size limit, unit B */
        "scrawlUrlPrefix": "http://ueditorci4.noor", /* Image Access Path Prefix */
        "scrawlInsertAlign": "none",

        /* Screenshot tool upload */
        "snapscreenActionName": "uploadimage", /* Action name for uploading screenshots */
        "snapscreenPathFormat": "/uploads/editors/image/{yyyy}{mm}{dd}/{time}", /* Upload save path, customizable save path and file name format */
        "snapscreenUrlPrefix": "http://ueditorci4.noor", /* Image Access Path Prefix */
        "snapscreenInsertAlign": "none", /* Floating method for inserted images */

        /* Grab remote image configuration */
        "catcherLocalDomain": ["127.0.0.1", "localhost", "img.baidu.com"],
        "catcherActionName": "catchimage", /* Execute the action name for capturing remote images */
        "catcherFieldName": "source", /* The name of the submitted image list form */
        "catcherPathFormat": "/uploads/editors/image/{yyyy}{mm}{dd}/{time}", /* Upload save path, customizable save path and file name format */
        "catcherUrlPrefix": "http://ueditorci4.noor", /* Image Access Path Prefix */
        "catcherMaxSize": 2048000, /* Upload size limit, unit B */
        "catcherAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* Grab image format display */

        /* Upload video configuration */
        "videoActionName": "uploadvideo", /* The action name for executing the uploaded video */
        "videoFieldName": "upfile", /* The name of the submitted video form */
        "videoPathFormat": "/uploads/editors/video/{yyyy}{mm}{dd}/{time}", /* Upload save path, customizable save path and file name format */
        "videoUrlPrefix": "http://ueditorci4.noor", /* Prefix for video access path */
        "videoMaxSize": 102400000, /* Upload size limit, unit B, default to 100MB */
        "videoAllowFiles": [
            ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
            ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",".m3u"], /* Upload video format display */

        /* Upload file configuration */
        "fileActionName": "uploadfile", /* In the controller, execute the action name for uploading videos */
        "fileFieldName": "upfile", /* The name of the submitted file form */
        "filePathFormat": "/uploads/editors/file/{yyyy}{mm}{dd}/{time}", /* Upload save path, customizable save path and file name format */
        "fileUrlPrefix": "http://ueditorci4.noor", /* File access path prefix */
        "fileMaxSize": 51200000, /* Upload size limit, unit B, default 50MB */
        "fileAllowFiles": [
            ".png", ".jpg", ".jpeg", ".gif", ".bmp",
            ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
            ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
            ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
            ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml",".m3u",".apk",".pdf"
        ], /* Upload file format display */

        /* List images in the specified directory */
        "imageManagerActionName": "listimage", /* Action name for executing image management */
        "imageManagerListPath": "/uploads/editorPhotos/image/", /* Specify the directory to list images in */
        "imageManagerListSize": 20, /* List the number of files each time */
        "imageManagerUrlPrefix": "", /* Image Access Path Prefix */
        "imageManagerInsertAlign": "none", /* Floating method for inserted images */
        "imageManagerAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* Listed file types */

        /* List files in the specified directory */
        "fileManagerActionName": "listfile", /* Action name for executing file management */
        "fileManagerListPath": "/uploads/editors/file/", /* Specify the directory to list files in */
        "fileManagerUrlPrefix": "", /* File access path prefix */
        "fileManagerListSize": 20, /* List the number of files each time */
        "fileManagerAllowFiles": [
            ".png", ".jpg", ".jpeg", ".gif", ".bmp",
            ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
            ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
            ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
            ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml",".m3u",".apk",".pdf"
        ] /* Listed file types */

    }';
    
}

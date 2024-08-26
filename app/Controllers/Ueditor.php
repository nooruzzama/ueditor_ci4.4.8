<?php
/**
 * Upload Controller CI4
 *
 * @package             UEditor Controller CI4
 * @author              Noor <noor-uzzama@outlook.com>
 * @version             V1.0
 * @copyright           Copyright 2024 UEditor CI4 Developed By Md Nooruzzama
 * @modifier            Noor <noor-uzzama@outlook.com>
 * @created             2024-06-29  
 */
namespace App\Controllers;
//use App\Libraries\Ueditorlib; 

use Config\UEditorConfig;
use Illuminate\Filesystem\Filesystem;
use CodeIgniter\Files\File; 
class Ueditor extends BaseController {

    //CI Superobjects
    private $myconfig;
    
    //Upload Configuration
    private $upload_params;
    
    //ueditor to configure
    private $ueditor_config;
    
    //Upload directory
    private $upload_path;
    
    //The data to be output
    private $output_data;
    
    //Callback Arguments 
    private $callback;

    protected $uri;
    protected $security;
    protected $input;
    protected $request;
    

	public function __construct()
    {
        
        $this->uri = service('uri');
        $this->security = \Config\Services::security();
        $this->input = \Config\Services::request();
        $this->request = \Config\Services::request();
        
        //Load Configuration
        $this->myconfig = new UEditorConfig(); 
        
        //Upload Configuration
        $this->upload_params = $this->myconfig->upload_params;
        
        //Upload directory
        $this->upload_path = $this->upload_params['upload_path'];
        
        //ueditor Upload configuration (remove carriage return, line feed, and whitespace)
        $this->ueditor_config =  json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $this->myconfig->ueditor_config), true);
        
        //Upload action
        
        $action = $this->input->getGet('action');
        
        switch($action){
            
            case 'config':
                $result = json_encode( $this->ueditor_config );
                break;
        
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $this->ueditor_config['imagePathFormat'],
                    "max_size" => $this->ueditor_config['imageMaxSize'],
                    "allowFiles" => $this->ueditor_config['imageAllowFiles']
                );
                $fieldName = $this->ueditor_config['imageFieldName'];
                $result = $this->uploadFile($config, $fieldName);
                break;
        
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $this->ueditor_config['scrawlPathFormat'],
                    "maxSize" => $this->ueditor_config['scrawlMaxSize'],
                    "allowFiles" => $this->ueditor_config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->ueditor_config['scrawlFieldName'];
                $result=$this->uploadBase64($config,$fieldName);
                break;
        
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $this->ueditor_config['videoPathFormat'],
                    "max_size" => $this->ueditor_config['videoMaxSize'],
                    "allowFiles" => $this->ueditor_config['videoAllowFiles']
                );
                $fieldName = $this->ueditor_config['videoFieldName'];
                $result=$this->uploadFile($config, $fieldName);
                break;
        
            case 'uploadfile':
                // default:
                $config = array(
                    "pathFormat" => $this->ueditor_config['filePathFormat'],
                    "max_size" => $this->ueditor_config['fileMaxSize'],
                    "allowFiles" => $this->ueditor_config['fileAllowFiles']
                );
                
                $fieldName = $this->ueditor_config['fileFieldName'];
                $result=$this->uploadFile($config, $fieldName);
                break;
        
            case 'listfile':
                $config=array(
                    'allowFiles' => $this->ueditor_config['fileManagerAllowFiles'],
                    'listSize' => $this->ueditor_config['fileManagerListSize'],
                    'path' => $this->ueditor_config['fileManagerListPath'],
                );
                $result = $this->listFile($config);
                break;
        
            case 'listimage':
                $config=array(
                    'allowFiles' => $this->ueditor_config['imageManagerAllowFiles'],
                    'listSize' => $this->ueditor_config['imageManagerListSize'],
                    'path' => $this->ueditor_config['imageManagerListPath'],
                );
                $result = $this->listFile($config);
                break;
                    
            case 'catchimage':
                $config = array(
                    "pathFormat" => $this->ueditor_config['catcherPathFormat'],
                    "maxSize" => $this->ueditor_config['catcherMaxSize'],
                    "allowFiles" => $this->ueditor_config['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $this->ueditor_config['catcherFieldName'];
                $result = $this->saveRemote($config , $fieldName);
                break;
        
            default:
                $result = json_encode(array('state'=> 'Noor Request error'));
                break;
                            
        }
        
        //Return value
        $this->callback = $this->input->getGet('callback');
        
        if ( $this->callback ) {
            if (preg_match("/^[\w_]+$/",  $this->callback )) {
                $this->output_data = htmlspecialchars( $this->callback ) . '(' . $result . ')';
            } else {
                $this->output_data = json_encode(array(
                    'state'=> 'callback Illegal parameters'
                ));
            }
        } else {
            $this->output_data = $result;
        }
        
    }

    /**
     * Upload file method
     *
     */
    private function uploadFile($config,$fieldName){
        
        //File path (in ueditor configuration)
        $flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
        
        $config['max_size']   =     $config['max_size'] ;// 设置附件上传大小
        $config['allowed_types']   =  self::_get_allow_files($config['allowFiles']);//允许上传文件的MIME类型
        $config['upload_path']  =     $this->upload_path.$flile_name; //上传路径
        $config['remove_spaces']  =     $this->upload_params['remove_spaces']; //文件名中的空格将被替换为下划线
        $config['encrypt_name']  =     $this->upload_params['encrypt_name']; //是否重命名文件
        
        //Create directory
        self::create_dir($config['upload_path']);
        
        //ci Upload class
        $get_file = $this->request->getFile($fieldName);
        $file_data=array();
        if ($get_file->isValid() && !$get_file->hasMoved()) {
            if ($get_file->getSize() <= $config['max_size'] * 1024) {
                $file_data['newName']=$get_file->getName();
                $file_data['tempName']=$get_file->getTempName();
                $file_data['filExtension']=$get_file->getClientExtension();
                $file_data['filMimeType']=$get_file->getClientMimeType();
                $file_data['filSizeByUnit']=$get_file->getSizeByUnit('kb');
                $file_data['filOriginal']=$get_file->getClientName();

                $path=$config['upload_path'];

                // Save file
                $newName = $get_file->getRandomName();
                $get_file->move($path, $newName);
 
                $pic_path = $this->upload_params['pic_path'].$flile_name.'/'.$newName;
                $data = array(
                        'state'=>"SUCCESS",
                        'url'=> $pic_path,
                        'title'=>$file_data['newName'],
                        'original'=>$file_data['newName'],
                        'file_ext'=> $file_data['filExtension'],
                        'size'=>$file_data['filSizeByUnit'],
                );
                /*$image = \Config\Services::image();
                if ($image->withFile($this->request->getFile($fieldName))->save($path)){
                    //Returned image path
                    $pic_path = $this->upload_params['pic_path'].$flile_name.'/'.$file_data['newName'];
                    $data = array(
                            'state'=>"SUCCESS",
                            'url'=> $pic_path,
                            'title'=>$file_data['newName'],
                            'original'=>$file_data['filOriginal'],
                            'file_ext'=> $file_data['filExtension'],
                            'size'=>$file_data['filSizeByUnit'],
                    );
                    
                }else{
                    
                    $data = array("state"=>'Upload Failed');
                }*/
            } else {
                return [
                    'state' => 'The file exceeds the size limit.',
                ];
            }
            
        }else{
            $data = ['errors' => 'The file has already been moved.'];
        }
    
        return json_encode($data);
    }
    
    
    
    /**
     * List all files in the folder, and if it is a directory, move downwards
     */
    private function listFile($config){
        $allowFiles = substr(str_replace(".", "|", join("", $config['allowFiles'])), 1);
        
        $size = $this->input->getGet('size') ? $this->input->getGet('size') : $config['listSize'];
        $start = $this->input->getGet('start') ? $this->input->getGet('start') : 0;
        $end = $start + $size;
    
        $path = $this->upload_path.$config['path'];
        
        $files = self::getfiles($path, $allowFiles);
        //return $files;
        if (!count($files)) {
            return json_encode(array(
                    "state" => "No matching files",
                    "list" => array(),
                    "start" => $start,
                    "total" => count($files)
            ));
        }
    
        /* Get a list of specified ranges */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
    
        /* Return data */
        $result = json_encode(array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $start,
                "total" => count($files)
        ));
    
        return $result;
    }
    
    
    
    /**
     *
     * Get remote images
     */
    private function saveRemote($config , $fieldName){
        $list = array();
        
        //Obtain resource name
        $source = $this->input->getPost( $fieldName );
        
        if(!$source){
            return json_encode(array(
                    'state'=>'The image cannot be empty'
            ));
        }
        
        foreach ($source as $imgUrl) {
    
            $imgUrl = htmlspecialchars($imgUrl);
            $imgUrl = str_replace("&amp;", "&", $imgUrl);
    
            //httpInitial verification
            if (strpos($imgUrl, "http") !== 0) {
                $data = array('state'=>'Not an HTTP link');
                return json_encode($data);
            }
            
            $heads = get_headers($imgUrl);
            //Format validation (extension validation and Content Type validation)
            $fileType = strtolower(strrchr($imgUrl, '.'));
            if (!in_array($fileType, $config['allowFiles']) || stristr($heads['Content-Type'], "image")) {
                $data = array("state"=>"Incorrect file format");
                return json_encode($data);
            }
             
            //Open the output buffer and retrieve remote images
            ob_start();
            $context = stream_context_create(
                    array('http' => array(
                            'follow_location' => false // don't follow redirects
                    ))
            );
            readfile($imgUrl, false, $context);
            $img = ob_get_contents();
            ob_end_clean();
            preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
             
            $path = $this->getFullPath($config['pathFormat']);
            if(strlen($img)>$config['maxSize']){
                $data['states'] = 'The file is too large';
                return json_encode($data);
            }
             
            $imgname = self::_get_rand_file_name().'.png';
            $oriName = $m ? $m[1]:"";
            
            $flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
            
            //Returned image path
            $pic_path = $this->upload_params['pic_path'].$flile_name.$imgname;
            
            //Upload Path
            $upload_path = $this->upload_path.$flile_name;
            
            //Create directory
            self::create_dir( $upload_path );
    
            if( file_put_contents($this->upload_path.$flile_name.$imgname, $img) ){
                array_push($list, array(
                "state" => 'SUCCESS',
                "url" => $pic_path,
                "size" => strlen($img),
                "title" => $imgname,
                "original" => $oriName,
                "source" => htmlspecialchars($imgUrl)
                ));
            }else{
                array_push($list,array('state'=>'File write failure'));
            }
        }
    
        /* Return to Grab Data */
        return json_encode(array(
                'state'=> count($list) ? 'SUCCESS':'ERROR',
                'list'=> $list
        ));
    }
    
    
    
    /**
     *
     *Parsing base64 encoding (graffiti)
     */
    private function uploadBase64($config,$fieldName){
        $data = array();
    
        $base64Data = $this->input->getPost($fieldName);
        
        $img = base64_decode($base64Data);
    
        if(strlen($img)>$config['maxSize']){
            $data['states'] = 'The file is too large';
            return json_encode($data);
        }
    
        //Replace random strings
        $imgname = self::_get_rand_file_name().'.png';
        
        $flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
        
        //Returned image path
        $pic_path = $this->upload_params['pic_path'].$flile_name.$imgname;
        
        //上传路径
        $upload_path = $this->upload_path.$flile_name;
        
        //Create directory
        self::create_dir( $upload_path );
        
        if( file_put_contents($this->upload_path.$flile_name.$imgname, $img) ){
        
            $data=array(
                    'state'=>'SUCCESS',
                    'url'=>$pic_path,
                    'title'=>$imgname,
                    'original'=>'scrawl.png',
                    'type'=>'.png',
                    'size'=>strlen($img),
                     
            );
        }else{
            $data=array(
                    'state'=>'The folder is not writable',
            );
        }
        return json_encode($data);
    }
    
    
    /**
     * Output results
     * @param data Array data
     * @return The result in JSON format after combination
     */
    public function index(){
        
        return $this->output_data;

        /*$Ueditorlib=new  Ueditorlib;
        //echo Ueditorlib::output_data();
        echo $Ueditorlib->output_data();*/
    }

    
    
    /**
     * Replace named files with rules
     * @param $path
     * @return string
     */
    static private function _get_full_path( $path )
    {
        //Replace date event
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $path;
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);
    
        return $format;
    }
    
    
    /**
    Obtain allowed file types
     * @param unknown $AllowFiles
     * @return string
     */
    static private function _get_allow_files($AllowFiles){
        $data = '';
        foreach ($AllowFiles as $key => $value) {
            $data .=ltrim($value,'.').'|';
        }
        return trim($data,'|');
    }
    
    /**
     * Create directory
     * @param unknown $path
     */
    static private  function create_dir($path) {
        if(!is_dir($path)){
            return mkdir($path, DIR_WRITE_MODE,true);//DIR_WRITE_MODE added this in app\config\constants.php
        }
    }
    
    
    /**
     * Obtain random file names
     */
    private function _get_rand_file_name(){
        return md5(uniqid());
    }
    
    
    /**
     * Traverse to retrieve files of the specified type in the directory
     * @param $path
     * @param array $files
     * @return array
     */
    function getfiles($path, $allowFiles='all', &$files = array()){

        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if($allowFiles!='all'){
                        if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                            $files[] = array(
                                    'url'=> substr($path2, strlen($this->upload_path)),
                                    'mtime'=> filemtime($path2)
                            );
                        }
                    }else{
                        $files[] = array(
                                'url'=> substr($path2, strlen($this->upload_path)),
                                'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

}

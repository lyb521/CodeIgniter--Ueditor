<?php
/**
 * ueditor扩展
 * 
 * @package 			controller
 * @author 	        	雷永波 <lyb19900227@126.com>
 * @version 		    V1.0
 * @copyright 	    	Copyright (c) 2014, www.leiyongbo.com
 * @modifier		          雷永波 <lyb19900227@126.com>
 * @lastmodifide		2014-12-27  下午4:13:05
 * 
 **/

class Ueditor{
	
	//CI超级对象
	private $CI;
	
	//上传配置
	private $upload_params;
	
	//ueditor配置
	private $ueditor_config;
	
	//上传目录
	private $upload_path;
	
	//要输出的数据
	private $output_data;
	
	//回调参数
	private $callback;
	
	
	/**
	 * 构造函数
	 */
	function __construct(){
		
		$this->CI = & get_instance(); 
		
		//载入配置
		$this->CI->config->load('upload');
		
		//上传配置
		$this->upload_params = $this->CI->config->item('upload_params');
		
		//上传目录
		$this->upload_path = $this->upload_params['upload_path'];
		
		//ueditor上传配置（去掉回车，换行，空白符）
		$this->ueditor_config =  json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $this->CI->config->item('ueditor_config')), true);
		
		//上传动作
		$action = $this->CI->input->get('action',true);
		
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
				$result = json_encode(array('state'=> '请求错误'));
				break;
							
		}
		
		//返回值
		$this->callback = $this->CI->input->get('callback',true);
		
		if ( $this->callback ) {
			if (preg_match("/^[\w_]+$/",  $this->callback )) {
				$this->output_data = htmlspecialchars( $this->callback ) . '(' . $result . ')';
			} else {
				$this->output_data = json_encode(array(
					'state'=> 'callback参数不合法'
				));
			}
		} else {
			$this->output_data = $result;
		}
		
	}
	
	
	/**
	 * 上传文件方法
	 *
	 */
	private function uploadFile($config,$fieldName){
		
		//文件路径（ueditor配置中）
		$flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
		
		$config['max_size']   =     $config['max_size'] ;// 设置附件上传大小
		$config['allowed_types']   =  self::_get_allow_files($config['allowFiles']);//允许上传文件的MIME类型
		$config['upload_path']  =     $this->upload_path.$flile_name; //上传路径
		$config['remove_spaces']  =     $this->upload_params['remove_spaces']; //文件名中的空格将被替换为下划线
		$config['encrypt_name']  =     $this->upload_params['encrypt_name']; //是否重命名文件
		
		//创建目录
		self::create_dir($config['upload_path']);
		
		//ci上传类
		$this->CI->load->library('upload',$config);
		
		if ($this->CI->upload->do_upload( $fieldName )){
			
			$image_data = $this->CI->upload->data();
		
			//返回的图片路径
			$pic_path = $this->upload_params['pic_path'].$flile_name.$image_data['file_name'];
		
			$data = array(
					'state'=>"SUCCESS",
					'url'=> $pic_path,
					'title'=>$image_data['file_name'],
					'original'=>$image_data['orig_name'],
					'file_ext'=> $image_data['file_ext'],
					'size'=>$image_data['file_size'],
			);
			
		}else{
			
			$data = array("state"=>$this->CI->upload->display_errors());
		}
		
		return json_encode($data);
	}
	
	
	
	/**
	 * 列出文件夹下所有文件，如果是目录则向下
	 */
	private function listFile($config){
		$allowFiles = substr(str_replace(".", "|", join("", $config['allowFiles'])), 1);
		
		$size = $this->CI->input->get('size',true) ? $this->CI->input->get('size',true) : $config['listSize'];
		$start = $this->CI->input->get('start',true) ? $this->CI->input->get('start',true) : 0;
		$end = $start + $size;
	
		$path = $this->upload_path.$config['path'];
		
		$files = self::getfiles($path, $allowFiles);
		//return $files;
		if (!count($files)) {
			return json_encode(array(
					"state" => "没有匹配的文件",
					"list" => array(),
					"start" => $start,
					"total" => count($files)
			));
		}
	
		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
			$list[] = $files[$i];
		}
	
		/* 返回数据 */
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
	 * 获取远程图片
	 */
	private function saveRemote($config , $fieldName){
		$list = array();
		
		//获得资源名称
		$source = $this->CI->input->get_post( $fieldName );
		
		if(!$source){
			return json_encode(array(
					'state'=>'图片不能为空'
			));
		}
		
		foreach ($source as $imgUrl) {
	
			$imgUrl = htmlspecialchars($imgUrl);
			$imgUrl = str_replace("&amp;", "&", $imgUrl);
	
			//http开头验证
			if (strpos($imgUrl, "http") !== 0) {
				$data = array('state'=>'不是http链接');
				return json_encode($data);
			}
			
			$heads = get_headers($imgUrl);
			//格式验证(扩展名验证和Content-Type验证)
			$fileType = strtolower(strrchr($imgUrl, '.'));
			if (!in_array($fileType, $config['allowFiles']) || stristr($heads['Content-Type'], "image")) {
				$data = array("state"=>"错误文件格式");
				return json_encode($data);
			}
			 
			//打开输出缓冲区并获取远程图片
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
				$data['states'] = '文件太大';
				return json_encode($data);
			}
			 
			$imgname = self::_get_rand_file_name().'.png';
			$oriName = $m ? $m[1]:"";
			
			$flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
			
			//返回的图片路径
			$pic_path = $this->upload_params['pic_path'].$flile_name.$imgname;
			
			//上传路径
			$upload_path = $this->upload_path.$flile_name;
			
			//创建目录
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
				array_push($list,array('state'=>'文件写入失败'));
			}
		}
	
		/* 返回抓取数据 */
		return json_encode(array(
				'state'=> count($list) ? 'SUCCESS':'ERROR',
				'list'=> $list
		));
	}
	
	
	
	/**
	 *
	 *解析base64编码(涂鸦)
	 */
	private function uploadBase64($config,$fieldName){
		$data = array();
	
		$base64Data = $this->CI->input->post($fieldName);
		
		$img = base64_decode($base64Data);
	
		if(strlen($img)>$config['maxSize']){
			$data['states'] = '文件太大';
			return json_encode($data);
		}
	
		//替换随机字符串
		$imgname = self::_get_rand_file_name().'.png';
		
		$flile_name = ltrim(self::_get_full_path($config['pathFormat']),'/');
		
		//返回的图片路径
		$pic_path = $this->upload_params['pic_path'].$flile_name.$imgname;
		
		//上传路径
		$upload_path = $this->upload_path.$flile_name;
		
		//创建目录
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
					'state'=>'文件夹不可写',
			);
		}
		return json_encode($data);
	}
	
	
	/**
	 * 输出结果
	 * @param data 数组数据
	 * @return 组合后json格式的结果
	 */
	public function output_data(){
		
		return $this->output_data;
	}
	
	
	
	/**
	 * 规则替换命名文件
	 * @param $path
	 * @return string
	 */
	static private function _get_full_path( $path )
	{
		//替换日期事件
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
	获得被允许的文件类型
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
	 * 创建目录
	 * @param unknown $path
	 */
	static private 	function create_dir($path) {
		if(!is_dir($path)){
			return mkdir($path, DIR_WRITE_MODE,true);
		}
	}
	
	
	/**
	 * 获得随机文件名
	 */
	private function _get_rand_file_name(){
		return md5(uniqid());
	}
	
	
	/**
	 * 遍历获取目录下的指定类型的文件
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
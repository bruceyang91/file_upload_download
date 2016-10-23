<?php

// sleep(10);
echo '<pre>';
// var_dump($_POST);
// var_dump($_FILES);
// move_uploaded_file($_FILES['logo']['tmp_name'], './upload.jpg');
// 后缀 mime 映射表
$ext2mime = array(
	'.jpeg' => 'image/jpeg',
	'.png'	=> 'image/png',
	'.gif'	=> 'image/gif',
	'.jpg'	=> 'image/jpeg',
	'.html'	=> 'text/html',
	// ....
	);

$result = uploadFile($_FILES['logo']);
var_dump($result);
/**
 * 上传单个文件
 * @param array $file 某个上传临时文件的5个信息数组
 * @return mixed 上传失败：false，上传成功：string返回文件名
 */
function uploadFile($file)
{
	// 判断错误
	if (0 != $file['error']) {
		// 存在错误
		trigger_error('上传文件存在错误');
		return false;
	}

	// 判断类型
	// 后缀的判断，防止绑定某些执行程序
	// 允许的后缀名
	$allow_ext_list = array('.jpg', '.jpeg', '.gif', '.png');
	// 从原始文件中，截取后缀部分，进行判断
	$ext = strrchr($file['name'], '.');
	// 判断是否允许
	if (! in_array($ext, $allow_ext_list)) {
		// 不是允许的后缀名，返回false
		trigger_error('文件类型有问题(后缀名)');
		return false;
	}
	// 判断MIME
	// 允许的MIME，获取文件的真实类型判断
	// $allow_mime_list = array('image/jpeg', 'image/png', 'image/gif');
	$allow_mime_list = getMIME($allow_ext_list);
	// 可以使用$file['type'],但是不准确。最好使用PHP程序自己获取，fileinfo扩展。
	$finfo = new Finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($file['tmp_name']);
	// 判断
	if (! in_array($mime, $allow_mime_list)) {
		trigger_error('文件的类型错误（MIME）');
		return false;
	}

	// 判断大小
	$allow_max_size = 1*1024*1024;//1M
	if ($file['size'] > $allow_max_size) {
		trigger_error('文件过大');
		return false;
	}

	// 移动，判断移动结果
	$upload_path = './upload/';

	// 建立子目录，以时间划分不同的大量的上传文件
	$subdir = date('YmdH') . '/';// 形成子目录名
	// 判断子目录是否已经存在
	if (! is_dir($upload_path . $subdir)) {
		// 子目录不存在，则需呀哦创建
		mkdir($upload_path . $subdir);
	}

	// 获取名字，科学起名
	$prefix = 'kang_';// 前缀
	$basename = uniqid($prefix, true) . $ext;

	// 移动
	$result_move = move_uploaded_file($file['tmp_name'], $upload_path . $subdir . $basename);
	if (!$result_move) {
		// 移动失败，空间满了，权限不足
		trigger_error('移动失败, 请联系服务器管理员');
		return false;
	}

	// 终于成功啦，返回上传的文件名
	return $subdir . $basename;

}



/**
 * @param array $ext_list 后缀名列表,array('.jpg', '.jpeg', '.gif', '.png')
 */
function getMIME($ext_list) {

	$mime_list = [];
	// 遍历每个后缀名
	foreach($ext_list as $value) {
		// 在映射表中，找到对应的MIME
		$mime_list[] = $GLOBALS['ext2mime'][$value];

	}
	return $mime_list;
}
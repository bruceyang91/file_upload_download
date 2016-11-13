<?php

$file='./temp.zip';

//获取文件类型
$finfo = new finfo(FILEINFO_MIME_TYPE);
$fileinfo = $finfo->file($file);

//获取文件名
$filename=basename($file);

//响应头
header('content-type:'.$fileinfo);
header('content-disposition:attachment ; filename='.$filename);

// 读取文件
$handler=fopen($file,'r');
while(!feof($handler)){
	echo fgets($handler,1024);
}

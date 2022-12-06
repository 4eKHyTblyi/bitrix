<?php
namespace Bitrix\KdaImportexcel;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Sftp
{
	protected $connects = array();
	protected $curConnect = array();
	
	public function GetConnect(&$path, $ftptimeout = 15, $checkPasv = true)
	{
		$path = trim($path);
		$ssl = preg_match("#^(ftps)://#", $path);
		$urlComponents = $this->ParseUrl($path);
		$filepath = $urlComponents['path'];
		$ftphost = $urlComponents['host']; 
		$ftpport = (isset($urlComponents['port']) ? $urlComponents['port'] : ($ssl ? 990 : 21));
		$ftpuser = (isset($urlComponents['user']) ? $urlComponents['user'] : 'anonymous');
		$ftppassword = (isset($urlComponents['pass']) ? $urlComponents['pass'] : '');
		$streamHash = md5($ftphost.'/'.$ftpport.'/'.((string)$ssl).'/'.$ftpuser.'/'.$ftppassword);
		if(!isset($this->connects[$streamHash]))
		{
			if($ssl) $stream = ftp_ssl_connect($ftphost, $ftpport, $ftptimeout);
			else $stream = ftp_connect($ftphost, $ftpport, $ftptimeout);
			if($stream)
			{
				if(ftp_login($stream, $ftpuser, $ftppassword))
				{
					ftp_set_option($stream, FTP_TIMEOUT_SEC, 2);
					ftp_set_option($stream, FTP_USEPASVADDRESS, false);
					ftp_pasv($stream, true);
					if($checkPasv && $this->IsEmptyResult($stream) && ftp_pasv($stream, false) && $this->IsEmptyResult($stream))
					{
						if(!ftp_pasv($stream, true))
						{
							return self::GetConnect($path, $ftptimeout, false);
						}
					}
					ftp_set_option($stream, FTP_TIMEOUT_SEC, $ftptimeout);
					$rootPath = ftp_pwd($stream);
					if(strlen($rootPath) > 0 && $rootPath!=='/' && strpos($rootPath, '/')===0 && (!is_array($this->lastNlistResult) || !in_array(preg_replace('/^(\/[^\/]+)\/.*$/', '$1', $filepath), $this->lastNlistResult)))
					{
						$path = preg_replace('/(ftps?:\/\/[^\/]+)\//Uis', '$1'.rtrim($rootPath, '/').'/', $path);
					}
					$this->connects[$streamHash] = $stream;
				}
				else
				{
					$this->connects[$streamHash] = false;
					ftp_close($stream);
				}
			}
		}
		$this->curConnect = $this->connects[$streamHash];
		return $this->curConnect;
	}
	
	public function __destruct()
	{
		foreach($this->connects as $hash=>$stream)
		{
			if($stream!==false)
			{
				ftp_close($stream);
			}
		}
	}
	
	public function IsEmptyResult($stream)
	{
		$list = $this->lastNlistResult = ftp_nlist($stream, '/'); 
		if(empty($list)) return true;
		else return false;
	}
	
	public function ParseUrl($path)
	{
		$urlComponents = parse_url($path);
		if(preg_match('/^(ftps?:\/\/)(.*):(.*)@(.*)$/Uis', $path, $m))
		{
			$path = $m[1].$m[4];
			$urlComponents = parse_url($path);
			$urlComponents['user'] = $m[2];
			//$urlComponents['pass'] = rawurldecode($m[3]);
			$urlComponents['pass'] = $m[3];
		}
		/*if(strpos($path, '#')!==false)
		{
			$path = str_replace('#', urlencode('#'), $path);
			$urlComponents = parse_url($path);
			if(isset($urlComponents['user'])) $urlComponents['user'] = urldecode($urlComponents['user']);
			if(isset($urlComponents['pass'])) $urlComponents['pass'] = urldecode($urlComponents['pass']);
		}*/
		if(isset($urlComponents["path"]))
		{
			$urlComponents["path"] = rawurldecode($urlComponents['path']);
			if(strpos($urlComponents["path"], '#')!==false)
			{
				$urlComponents = array_merge($urlComponents, parse_url($urlComponents["path"]));
			}
		}
		return $urlComponents;
	}
	
	public function Upload($path, $fn)
	{
		$path = trim($path);
		if((!preg_match("#^(ftp)://#i", $path) && function_exists('ftp_connect')
			&& !preg_match("#^(ftps)://#i", $path) && function_exists('ftp_ssl_connect'))
			|| !$this->GetConnect($path)) return false;
		$urlComponents = $this->ParseUrl($path);
		$filepath = $urlComponents["path"];
		$fp = fopen($fn, 'r');
		$res = ftp_fput($this->curConnect, $filepath, $fp, FTP_BINARY);
		fclose($fp);		
		return $res;
	}
	
	public function SaveFile($temp_path, $filepath)
	{
		if(!$this->curConnect) return false;
		$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		$res = ftp_get($this->curConnect, $temp_path, $filepath, FTP_BINARY);
		if(!$res && (!defined('BX_UTF') || !BX_UTF))
		{
			$res = ftp_get($this->curConnect, $temp_path, \Bitrix\Main\Text\Encoding::convertEncoding($filepath, 'CP1251', 'UTF-8'), FTP_BINARY);
		}
		if(!$res && (defined('BX_UTF') && BX_UTF))
		{
			$res = ftp_get($this->curConnect, $temp_path, \Bitrix\Main\Text\Encoding::convertEncoding($filepath, 'UTF-8', 'CP1251'), FTP_BINARY);
		}
	}
	
	public function GetListFiles($path)
	{
		$arFiles = array();
		if(isset($this->currentDirPath) && $this->currentDirPath==$path)
		{
			$arFiles = $this->currentDirFiles;
		}
		else
		{
			if((preg_match("#^(ftp)://#", $path) && function_exists('ftp_connect')
				|| preg_match("#^(ftps)://#", $path) && function_exists('ftp_ssl_connect')))
			{
				if($this->GetConnect($path))
				{
					$urlComponents = $this->ParseUrl($path);				
					$dirpath = $urlComponents["path"];
					$arFiles = false;
					if(function_exists('ftp_mlsd'))
					{
						$arFiles = ftp_mlsd($this->curConnect, $dirpath);
						if(is_array($arFiles))
						{
							usort($arFiles, array(__CLASS__, 'SortByModify'));
							$arFiles = array_diff(array_map(array(__CLASS__, 'GetNameFromArray'), $arFiles), array('.', '..'));
							$dirpath = '/'.trim($dirpath).'/';
							foreach($arFiles as $k=>$v)
							{
								$arFiles[$k] = $dirpath.$v;
							}
						}
					}
					if(!is_array($arFiles))
					{
						$arFiles = ftp_nlist($this->curConnect, $dirpath);
					}
				}
			}
			$this->currentDirPath = $path;
			$this->currentDirFiles = $arFiles;
		}
		return $arFiles;
	}
	
	public function MakeFileArray($path, $arParams=array(), $bMultiple = false)
	{
		if((preg_match("#^(ftp)://#", $path) && function_exists('ftp_connect')
			|| preg_match("#^(ftps)://#", $path) && function_exists('ftp_ssl_connect')))
		{
			$bMultiple = (bool)($arParams['MULTIPLE']=='Y');
			$temp_path = '';
			$bExternalStorage = false;
			foreach(GetModuleEvents("main", "OnMakeFileArray", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array($path, &$temp_path)))
				{
					$bExternalStorage = true;
					break;
				}
			}
			
			if(!$bExternalStorage)
			{
				if($this->GetConnect($path, ($arParams['TIMEOUT'] ? $arParams['TIMEOUT'] : 15)))
				{
					$path = trim($path);
					$fileName = bx_basename($path);
					$arUrl = $this->ParseUrl($path);
					$relPath = $arUrl['path'];
					$fileTypes = array();
					$bNeedImage = (bool)($arParams['FILETYPE']=='IMAGE');
					if($bNeedImage) $fileTypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
					elseif($arParams['FILE_TYPE']) $fileTypes = array_diff(array_map('trim', explode(',', ToLower($arParams['FILE_TYPE']))), array(''));
					if(substr($path, -1)=='/')
					{
						$arDirFiles = array_values(array_map(array(__CLASS__, 'GetFileFromPath'), $this->GetListFiles($path)));
						$arDirFiles = array_diff($arDirFiles, array('.', '..'));
						if($bMultiple)
						{
							$arFiles = array();
							foreach($arDirFiles as $file)
							{
								if(in_array(ToLower(end(explode('.', $file))), $fileTypes) || empty($fileTypes))
								{
									$arFiles[] = $this->MakeFileArray($path.$file, $arParams);
								}
							}
							return $arFiles;
						}
						rsort($arDirFiles);
						$findFile = false;
						$i = 0;
						while(!$findFile && isset($arDirFiles[$i]))
						{
							if(in_array(ToLower(end(explode('.', $arDirFiles[$i]))), $fileTypes) || empty($fileTypes))
							{
								$findFile = true;
								$path .= $arDirFiles[$i];
							}
							$i++;
						}
					}
					elseif(self::PathContainsMask($relPath))
					{
						$path = substr($path, 0, -strlen($relPath));
						while(preg_match('/^(\/[^\/]*)(\/.*$)/', $relPath, $m) && !self::PathContainsMask($m[1]))
						{
							$path = $path.$m[1];
							$relPath = $m[2];
						}
						
						if(strlen($relPath) > 0) $path = $path.'/';
						list($fileName, $relPath) = explode('/', ltrim($relPath, '/'), 2);
						if(strlen($relPath) > 0) $relPath = '/'.$relPath;
						
						$arDirFiles = array_values(array_map(array(__CLASS__, 'GetFileFromPath'), $this->GetListFiles($path)));
						$arDirFiles = array_diff($arDirFiles, array('.', '..'));
						rsort($arDirFiles);
						$arFiles = array();
						if(is_array($arDirFiles))
						{
							foreach($arDirFiles as $file)
							{
								if(preg_match(self::GetPatternForRegexp($fileName), $file) && (in_array(ToLower(end(explode('.', $file))), $fileTypes) || empty($fileTypes)))
								{
									$arFiles[] = $path.$file.$relPath;
								}
							}
						}

						if($bMultiple)
						{
							foreach($arFiles as $k=>$file)
							{
								$arFiles[$k] = $this->MakeFileArray($file, $arParams);
							}
							return $arFiles;
						}
						elseif(count($arFiles) > 0)
						{
							return $this->MakeFileArray(current($arFiles), $arParams);
						}
					}
					
					$urlComponents = $this->ParseUrl($path);
					if ($urlComponents && strlen($urlComponents["path"]) > 0)
					{
						$temp_path = \CFile::GetTempName('', bx_basename($urlComponents["path"]));
					}
					else
						$temp_path = \CFile::GetTempName('', bx_basename($path));
					
					$filepath = $urlComponents["path"];
					$this->SaveFile($temp_path, $filepath);
				}
				$arFile = \CFile::MakeFileArray($temp_path);
			}
			elseif($temp_path)
			{
				$arFile = \CFile::MakeFileArray($temp_path);
			}
			
			if(strlen($arFile["type"])<=0)
				$arFile["type"] = "unknown";
		}
		else
		{
			$arFile = \CFile::MakeFileArray($path);
		}
		return $arFile;
	}
	
	public function PathContainsMask($path)
	{
		return (bool)((strpos($path, '*')!==false || (strpos($path, '{')!==false && strpos($path, '}')!==false)));
	}
	
	public static function GetPatternForRegexp($pattern)
	{
		$pattern = preg_quote($pattern, '/');
		$pattern = preg_replace_callback('/\\\{([^\}]*)\\\}/', array(__CLASS__, 'GetPatternCallback'), $pattern);
		$pattern = strtr($pattern, array('\*'=>'.*', '\?'=>'.'));
		return '/'.$pattern.'/';
	}
	
	public static function GetPatternCallback($m)
	{
		return "(".str_replace(",", "|", $m[1]).")";
	}
	
	public static function SortByModify($a, $b)
	{
		return $a["modify"]>$b["modify"] ? -1 : 1;
	}
	
	public static function GetNameFromArray($n)
	{
		return $n["name"];
	}
	
	public static function GetFileFromPath($n)
	{
		return end(explode("/", $n));
	}
}
<?php
namespace Bitrix\KdaImportexcel;

class ZipArchive
{
	private $tmpDir = '';
	private $removeOnClose = false;
	private $sStringFile = false;
	private $strIndexes = array();
	
	public function __construct()
	{

	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function close()
	{
		if(strlen($this->tmpDir) > 0 && file_exists($this->tmpDir) && $this->removeOnClose)
		{
			static::RemoveFileDir($this->tmpDir);
		}
		$this->removeOnClose = false;
		$this->sStringFile = false;
		$this->strIndexes = array();
		$this->tmpDir = '';
	}
	
	public static function RemoveFileDir($dir)
	{
		if(is_file($dir)) $dir = static::GetFileDir($dir);
		elseif(is_numeric($dir)) $dir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.IUtils::$moduleId.'/'.IUtils::$moduleSubDir.'_archives/'.$dir.'/';
		if($dir && is_dir($dir))
		{
			if(strpos($dir, $_SERVER['DOCUMENT_ROOT'])===0)
			{
				DeleteDirFilesEx(mb_substr($dir, mb_strlen($_SERVER['DOCUMENT_ROOT'])));
			}
			else
			{
				self::DeleteDirFiles($dir);
			}
			$pDir = dirname($dir);
			if(($arFiles = scandir($pDir)) && is_array($arFiles) && count(array_diff($arFiles, array('.', '..')))==0) rmdir($pDir);
		}
	}
	
	public static function DeleteDirFiles($path)
	{
		if(strlen($path) == 0 || $path == '/')
			return false;

		$full_path = $path;
		$full_path = preg_replace("#[\\\\\\/]+#", "/", $full_path);

		$f = true;
		if(is_file($full_path) || is_link($full_path))
		{
			if(@unlink($full_path))
				return true;
			return false;
		}
		elseif(is_dir($full_path))
		{
			if($handle = opendir($full_path))
			{
				while(($file = readdir($handle)) !== false)
				{
					if($file == "." || $file == "..")
						continue;

					if(!self::DeleteDirFiles($path."/".$file))
						$f = false;
				}
				closedir($handle);
			}
			if(!@rmdir($full_path))
				return false;
			return $f;
		}
		return false;
	}
	
	public static function GetFileDir($pFilename)
	{
		if(($pos = mb_strpos($pFilename, '/'.IUtils::$moduleId.'/'))!==false)
		{
			$filePath = \Bitrix\Main\IO\Path::convertPhysicalToLogical(mb_substr($pFilename, $pos + 1));
			$fileName = bx_basename($filePath);
			$subDir = mb_substr($filePath, 0, -mb_strlen($fileName) - 1);
			if(strlen($fileName) > 0 && strlen($subDir) > 0 && ($arFile = \CFile::GetList(array(), array('SUBDIR'=>$subDir, 'FILE_NAME'=>$fileName))->Fetch()))
			{
				return $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.IUtils::$moduleId.'/'.IUtils::$moduleSubDir.'_archives/'.$arFile['ID'].'/';
			}
		}
		return false;
	}
	
	public function open($pFilename)
	{
		$this->tmpDir = '';
		$this->removeOnClose = false;
		$this->sStringFile = false;
		$this->strIndexes = array();
		if($dir = static::GetFileDir($pFilename))
		{
			$this->tmpDir = $dir;
			if(file_exists($this->tmpDir))
			{
				if(filemtime($this->tmpDir) < max(filemtime($pFilename), time()-24*60*60) || $this->calcCheckSum()!=$this->getCheckSum())
				{
					DeleteDirFilesEx(mb_substr($this->tmpDir, mb_strlen($_SERVER['DOCUMENT_ROOT'])));
					rmdir(dirname($this->tmpDir));
				}
				else
				{
					return true;
				}
			}
			if(!file_exists($this->tmpDir))
			{
				\Bitrix\Main\IO\Directory::createDirectory($this->tmpDir);
			}
		}
				
		if(strlen($this->tmpDir)==0)
		{
			$this->removeOnClose = true;
			$temp_path = \CFile::GetTempName('', bx_basename($pFilename));
			$tmpDir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
			\Bitrix\Main\IO\Directory::createDirectory($tmpDir);
			while(($this->tmpDir = $tmpDir.'/'.md5(mt_rand()).'/') && file_exists($this->tmpDir) && $i<1000)
			{
				$i++;
			}
		}
		
		if(class_exists('\ZipArchive') && ($zipObj = new \ZipArchive) && ($zipObj->open($pFilename) === true) && $zipObj->numFiles > 0)
		{
			$zipObj->extractTo($this->tmpDir);
			$zipObj->close();
			$this->setCheckSum();
			return true;
		}
		else
		{
			$io = \CBXVirtualIo::GetInstance();
			$pFilename2 = $io->GetLogicalName($pFilename);
			$zipObj = \CBXArchive::GetArchive($pFilename2, 'ZIP');
			if($zipObj->Unpack($this->tmpDir)!==false)
			{
				$this->setCheckSum();
				return true;
			}
			else
			{
				@exec('unzip "'.$pFilename.'" -d '.$this->tmpDir);
				if(count(array_diff(scandir($this->tmpDir), array('.', '..')))==0)
				{
					$this->setCheckSum();
					return true;
				}
			}
		}
		return false;
	}
	
	public function setCheckSum()
	{
		$sum = $this->calcCheckSum();
		file_put_contents($this->tmpDir.'/.checksum', $sum);
	}
	
	public function getCheckSum()
	{
		if(!file_exists($this->tmpDir.'/.checksum')) return '';
		return file_get_contents($this->tmpDir.'/.checksum');
	}
	
	public function calcCheckSum($dir='')
	{
		if(strlen($dir)==0) $dir = $this->tmpDir;
		$dir = rtrim($dir, '/').'/';
		$arFiles = scandir($dir);
		$arFiles = array_diff($arFiles, preg_grep('/^(\.+|\.checksum|.*\.cache)$/i', $arFiles));
		$sum = implode('#', $arFiles);
		foreach($arFiles as $k=>$v)
		{
			if(is_dir($dir.$v))
			{
				$sum .= '###'.$this->calcCheckSum($dir.$v);
			}
		}
		return md5($sum);
	}
	
	public function getFromName($name, $length=0, $flags=0)
	{
		$content = file_get_contents($this->tmpDir.$name);
		if($length > 0) $content = substr($content, 0, $length);
		return $content;
	}
	
	public function normalizeFileContent($fn)
	{
		//<x:tags fixes
		if(strpos(file_get_contents($fn, false, null, 0, 1000), '<x:')!==false)
		{
			$fnTmp = $fn.'.tmp';
			copy($fn, $fnTmp);
			$handle = fopen($fnTmp, 'r');
			$handle2 = fopen($fn, 'w');
			$buffer = $bufferPart = '';
			while(!feof($handle)) 
			{
				$buffer = $bufferPart.fread($handle, 4*1024*1024);
				if(($pos = strrpos($buffer, '<'))!==false)
				{
					$bufferPart = substr($buffer, $pos);
					$buffer = substr($buffer, 0, $pos);
				}
				else $bufferPart = '';
				$buffer = strtr($buffer, array('<x:'=>'<', '</x:'=>'</'));
				fwrite($handle2, $buffer);
			}
			fclose($handle2);
			fclose($handle);
			unlink($fnTmp);
		}
	}
	
	public function getSimpleXmlForSheet($name, $readFilter = null)
	{
		$fn = $this->tmpDir.$name;

		if(!file_exists($fn))
		{
			return new \SimpleXMLElement('<d></d>');
		}
		$this->normalizeFileContent($fn);

		$xmlClass = $this->getXmlReaderClass();
		$xml = new $xmlClass();
		$res = $xml->open($fn);

		$firstRow = (is_callable(array($readFilter, 'getStartRow')) ? $readFilter->getStartRow() : 1);
		$lastRow = (is_callable(array($readFilter, 'getEndRow')) ? $readFilter->getEndRow() : 999999);
		$extraLines = (is_callable(array($readFilter, 'getLoadLines')) ? $readFilter->getLoadLines() : array());
		$arColumns = (is_callable(array($readFilter, 'getColumns')) ? $readFilter->getColumns() : null);
		$xmlObj = new \SimpleXMLElement('<d></d>');
		$arObjects = array();
		$arObjectNames = array();
		$curDepth = 0;
		$arObjects[$curDepth] = &$xmlObj;
		$rowNum = 0;
		$isRead = false;
		while (($isRead || $xml->read())) {
			$isRead = false;
			if($xml->nodeType == $xmlClass::ELEMENT) 
			{
				if($arObjectNames[1]=='sheetData' && $xml->name=='row' && $xml->depth==2)
				{					
					$arObjectNames[$xml->depth] = $xml->name;
					$this->SetRowNum($rowNum, $xml);
					if($rowNum > 1)
					{
						while($rowNum < $firstRow-1 && !in_array($rowNum, $extraLines) && ($isRead = true) && $xml->next('row'))
						{
							$this->SetRowNum($rowNum, $xml);
						}
						if($rowNum > $lastRow)
						{
							$time0 = time();
							$maxTime = 5;
							while($xml->read() && ($xml->nodeType != $xmlClass::ELEMENT || $xml->depth > 1) && time()-$time0 < $maxTime)
							{
								if($xml->nodeType == $xmlClass::ELEMENT && $xml->name=='row' && $xml->depth==2){$rowNum++;}
							}
							if(time()-$time0 >= $maxTime) break;
							//while($xml->next('row')){$rowNum++;}
							$xmlObj->addChild('rowsMaxIndex', $rowNum);
							$isRead = true;
							continue;
						}
					}
				}
				elseif(isset($arColumns) && is_array($arColumns) && $arObjectNames[2]=='row' && $xml->name=='c' && $xml->depth==3)
				{					
					$arObjectNames[$xml->depth] = $xml->name;
					while($xml->name=='c' && !in_array(preg_replace('/\d+/', '', $xml->getNodeAttribute('r', false)), $arColumns))
					{
						$isRead = true;
						while($xml->read() && ($xml->nodeType != $xmlClass::ELEMENT || (!($xml->name=='row' && $xml->depth==2) && !($xml->name=='c' && $xml->depth==3)))){
							
						}
					}
					if($xml->name!='c') continue;
				}
				/*if($arObjectNames[1]=='sheetData' && $arObjectNames[2]=='row' && $xml->depth>=2)
				{
					if(is_callable(array($readFilter, 'readCell')) && !$readFilter->readCell(1, $rowNum)) continue;
				}*/

				$arAttributes = $xml->getNodeAttributes();

				if($xml->depth > 0)
				{
					$curDepth = $xml->depth;
					$arObjectNames[$curDepth] = $xml->name;
					$curName = $xml->name;
					$curValue = null;
					$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);
					$curValue = $xml->getNodeValue($isRead);

					$curValue = str_replace('&', '&amp;', $curValue);
					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();
		
		$strIndexes = array();
		if(isset($xmlObj->sheetData) && isset($xmlObj->sheetData->row))
		{
			foreach($xmlObj->sheetData->row as $row)
			{
				if(isset($row->c))
				{
					foreach($row->c as $cell)
					{
						if(isset($cell->v))
						{
							$strIndexes[(int)$cell->v] = (int)$cell->v;
						}
					}
				}
			}
		}
		$this->strIndexes = $strIndexes;

		return $xmlObj;
	}
	
	public function SetRowNum(&$rowNum, $xml)
	{
		$r = $xml->getNodeAttribute('r', false);
		if($r!==false) $rowNum = $r;
		else $rowNum++;
	}
	
	public function setSharedStringsFile($name)
	{
		$fn = $this->tmpDir.$name;

		if(!file_exists($fn))
		{
			$fname = bx_basename($fn);
			$fchar = substr($fname, 0, 1);
			if(strtoupper($fchar) == $fchar) $fchar = strtolower($fchar);
			else $fchar = strtoupper($fchar);
			$fname = $fchar.substr($fname, 1);
			$fn = substr($fn, 0, -strlen($fname)).$fname;
		}

		if(file_exists($fn))
		{
			$this->normalizeFileContent($fn);
			$this->sStringFile = $fn;
		}
	}
	
	public function getXmlReaderClass()
	{
		//return (class_exists('\XMLReader') ? '\XMLReader' : '\Bitrix\KdaImportexcel\XMLReader');
		return '\Bitrix\KdaImportexcel\XMLReader';
	}
	
	public function getSharedStringsFromIndexes($reader)
	{
		$sharedStrings = array();
		if($this->sStringFile===false || !file_exists($this->sStringFile) || !is_array($this->strIndexes) || empty($this->strIndexes)) return $sharedStrings;
		
		$xmlClass = $this->getXmlReaderClass();
		$xml = new $xmlClass();
		$res = $xml->open($this->sStringFile);

		$find = false;
		while($xml->read() && !($xml->nodeType==$xmlClass::ELEMENT && $xml->name=='si' && $xml->depth==1 && ($find = true))){}
		if(!$find) return $sharedStrings;
		
		$ind = -1;
		while(++$ind==0 || $xml->next('si'))
		{
			if(!isset($this->strIndexes[$ind])) continue;
			$val = simplexml_load_string($xml->readOuterXml());
		
			if (isset($val->t)) {
				$sharedStrings[$ind] = \KDAPHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $val->t );
			} elseif (isset($val->r)) {
				$sharedStrings[$ind] = (is_callable(array($reader, 'publicParseRichText')) ? $reader->publicParseRichText($val) : '');
			}
		}
		$xml->close();

		return $sharedStrings;
	}
	
	public function getSharedStringsFromString($str, $reader)
	{
		$tmpDir = $this->tmpDir;
		$name = 'sharedStrings.xml';
		$tempPath = \CFile::GetTempName('', $name);
		$dir = \Bitrix\Main\IO\Path::getDirectory($tempPath);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		$this->tmpDir = rtrim($dir, '/').'/';
		file_put_contents($tempPath, $str);
		$sharedStrings = $this->getSharedStrings($name, $reader, false);
		unlink($tempPath);
		if(($arFiles = scandir($dir)) && is_array($arFiles) && count(array_diff($arFiles, array('.', '..')))==0) rmdir($dir);
		$this->tmpDir = $tmpDir;
		return $sharedStrings;
	}
	
	public function getSharedStrings($name, $reader, $bCache=false)
	{
		$fn = $this->tmpDir.$name;
		$sharedStrings = array();

		if(!file_exists($fn))
		{
			$fname = bx_basename($fn);
			$fchar = substr($fname, 0, 1);
			if(strtoupper($fchar) == $fchar) $fchar = strtolower($fchar);
			else $fchar = strtoupper($fchar);
			$fname = $fchar.substr($fname, 1);
			$fn = substr($fn, 0, -strlen($fname)).$fname;
		}
		
		if(!file_exists($fn))
		{
			return $sharedStrings;
		}
		
		$fnCache = $fn.'.cache';
		if(!$bCache || !file_exists($fnCache) || filemtime($fn) > filemtime($fnCache))
		{
			$xml = new \XMLReader();
			$res = $xml->open($fn);

			while ($xml->read()) {
				if($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 'si' && $xml->depth == 1) 
				{
					$val = new \SimpleXMLElement('<si></si>');
					$arObjects = array();
					$arObjectNames = array();
					$curDepth = $xml->depth;
					$arObjects[$curDepth] = &$val;
					$isRead = false;
					while (($isRead || $xml->read())
						&& !($xml->nodeType == \XMLReader::END_ELEMENT && $xml->name == 'si' && $xml->depth == 1)) {
						$isRead = false;
						if($xml->nodeType == \XMLReader::ELEMENT) 
						{
							$arAttributes = array();
							if($xml->moveToFirstAttribute())
							{
								$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
								while($xml->moveToNextAttribute ())
								{
									$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
								}
							}
							$xml->moveToElement();
					
							if($xml->depth > 1)
							{
								$curDepth = $xml->depth;
								$arObjectNames[$curDepth] = $xml->name;
								$curName = $xml->name;
								$curValue = null;
								$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);

								while($xml->read() && $xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE){}
								if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
								{
									$curValue = $xml->value;
								}
								else
								{
									$isRead = true;
								}

								$curValue = str_replace('&', '&amp;', $curValue);
								$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
							}
							
							foreach($arAttributes as $arAttr)
							{
								if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
								else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
							}
						}
					}
					
					if (isset($val->t)) {
						$sharedStrings[] = \KDAPHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $val->t );
					} elseif (isset($val->r)) {
						$sharedStrings[] = (is_callable(array($reader, 'publicParseRichText')) ? $reader->publicParseRichText($val) : '');
					}
				}
			}
			$xml->close();
			
			if($bCache)
			{
				if(file_exists($fnCache)) unlink($fnCache);
				$handle = fopen($fnCache, 'a');
				foreach($sharedStrings as $k=>$str)
				{
					fwrite($handle, ($k > 0 ? "\r\n" : '').base64_encode(serialize($str)));
				}
				fclose($handle);
			}
		}
		else
		{
			$handle = fopen($fnCache, "r");
			while(!feof($handle))
			{
				$buffer = fgets($handle, 65536);
				$sharedStrings[] = unserialize(base64_decode($buffer));
			}
			fclose($handle);

		}
		return $sharedStrings;
	}
	
	public function locateName($name, $flags=0)
	{
		if(file_exists($this->tmpDir.$name))
		{
			return 1;
		}
		return false;
	}
	
	public function statName($name, $flags=0)
	{
		if(file_exists($this->tmpDir.$name))
		{
			return array(
				'name' => $name,
				'index' => 1,
				'crc' => crc32(file_get_contents($this->tmpDir.$name)),
				'size' => filesize($this->tmpDir.$name),
				'mtime' => filemtime($this->tmpDir.$name),
				'comp_size' => filesize($this->tmpDir.$name),
				'comp_method' => 8
			);
		}
		return false;
	}
	
	public function getZipFilePath($subpath, $createTmp = false)
	{
		$subpath = str_replace('\\', '/', $subpath);
		$subpath = ltrim($subpath, '/');
		$path = $this->tmpDir.$subpath;
		if($createTmp)
		{
			$temp_path = \CFile::GetTempName('', bx_basename($path));
			$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
			\Bitrix\Main\IO\Directory::createDirectory($dir);
			copy($path, $temp_path);
			return $temp_path;
		}
		else
		{
			return $path;
		}
	}
}

if(class_exists('\XMLReader'))
{
	class XmlReader extends \XMLReader
	{
		public function getNodeAttributes()
		{
			$arAttributes = array();
			if($this->moveToFirstAttribute())
			{
				$arAttributes[] = array('name'=>$this->name, 'value'=>$this->value, 'namespaceURI'=>$this->namespaceURI);
				while($this->moveToNextAttribute())
				{
					$arAttributes[] = array('name'=>$this->name, 'value'=>$this->value, 'namespaceURI'=>$this->namespaceURI);
				}
			}
			$this->moveToElement();
			return $arAttributes;
		}
		
		public function getNodeAttribute($k, $v=false)
		{
			$r = $v;
			if($this->moveToFirstAttribute())
			{
				if($this->name==$k) $r = $this->value;
				while($r===false && $this->moveToNextAttribute())
				{
					if($this->name==$k) $r = $this->value;
				}
			}
			$this->moveToElement();
			return $r;
		}
		
		public function getNodeValue(&$isRead)
		{
			$this->read();
			if($this->nodeType == self::TEXT)
			{
				return $this->value;
			}
			else
			{
				$isRead = true;
			}
			return null;
		}
	}
}
else
{
	class XmlReader
	{
		const ELEMENT = 1;
		
		var $charset = false;
		var $element_stack = false;
		var $depth_stack = false;
		var $file_position = 0;

		var $fp = null;
		var $read_size = 1024;
		var $buf = "";
		var $buf_position = 0;
		var $buf_len = 0;
		var $last_id = 0;
		var $name = null;
		var $depth = null;
		var $namespaceURI = null;
		var $item = array();

		public function __construct($NS = array(), $read_size = 1024)
		{
			if(!array_key_exists("charset", $NS))
				$NS["charset"] = false;
			$this->charset = $NS["charset"];
			
			if(!array_key_exists("depth_stack", $NS))
				$NS["depth_stack"] = array();
			$this->depth_stack = $NS["depth_stack"];

			if(!array_key_exists("element_stack", $NS))
				$NS["element_stack"] = array();
			$this->element_stack = $NS["element_stack"];

			if(!array_key_exists("file_position", $NS))
				$NS["file_position"] = 0;
			$this->file_position = $NS["file_position"];
			
			$this->read_size = $read_size;
		}
		
		public function open($fn)
		{
			if(!file_exists($fn)) return false;
			$this->fp = fopen($fn, 'rb');
			fseek($this->fp, $this->file_position);
		}
		
		public function close()
		{
			fclose($this->fp);
		}
		
		public function getNodeAttributes()
		{
			$arAttributes = array();
			if(isset($this->item['ATTRIBUTES']) && is_array($this->item['ATTRIBUTES']))
			{
				foreach($this->item['ATTRIBUTES'] as $k=>$v)
				{
					if(strpos($k, ':')!==false) 
					{
						list($k1, $k2) = explode(':', $k, 2);
					} 
					else
					{
						$k1 = null; 
						$k2 = $k;
					}
					$arAttributes[] = array('name'=>$k2, 'value'=>$v, 'namespaceURI'=>$k1);
				}
			}
			return $arAttributes;
		}
		
		public function getNodeAttribute($k, $v=false)
		{
			if(isset($this->item['ATTRIBUTES']) && is_array($this->item['ATTRIBUTES']) && array_key_exists($k, $this->item['ATTRIBUTES']))
			{
				return $this->item['ATTRIBUTES'][$k];
			}
			return $v;
		}
		
		public function getNodeValue(&$isRead)
		{
			if(array_key_exists('VALUE', $this->item)) return $this->item['VALUE'];
			return null;
		}

		public function GetFilePosition()
		{
			return $this->file_position;
		}
		
		public function GetNsParams()
		{
			return array(
				'charset' => $this->charset,
				'element_stack' => $this->element_stack,
				'depth_stack' => $this->depth_stack,
				'file_position' => $this->file_position
			);
		}

		public function read($b=true)
		{
			if(!$this->fp) return false;
			$cs = $this->charset;
			while(($xmlChunk = $this->_get_xml_chunk($this->fp)) !== false)
			{
				if($cs)
				{
					$xmlChunk = \Bitrix\Main\Text\Encoding::convertEncoding($xmlChunk, $cs, LANG_CHARSET);
				}

				if($xmlChunk[0] == "/")
				{
					if($this->_end_element($xmlChunk) && !$b) return true;
				}
				elseif($xmlChunk[0] == "!" || $xmlChunk[0] == "?")
				{
					if(strncmp($xmlChunk, "?xml", 4) === 0)
					{
						if(preg_match('#encoding[\s]*=[\s]*"(.*?)"#i', $xmlChunk, $arMatch))
						{
							$this->charset = $arMatch[1];
							if(strtoupper($this->charset) === strtoupper(LANG_CHARSET))
								$this->charset = false;
							$cs = $this->charset;
						}
					}
				}
				else
				{
					if($this->_start_element($xmlChunk) && $b) return true;
				}
			}

			return !feof($this->fp);
		}
		
		public function next($n)
		{
			$d = $this->depth;
			$res = false;
			while($this->read() && !($this->name==$n && $this->depth==$d && ($res = true)) && $this->depth>=$d){}

			return $res;
		}
		
		public function readOuterXml()
		{
			$d = $this->depth;
			$n = $this->name;
			$res = false;
			while($this->read(false) && !($this->name==$n && $this->depth==$d && ($res = true)) && $this->depth>=$d){}
			$xml = '';
			if($res)
			{
				$c = ftell($this->fp);
				$arItem = end($this->depth_stack);
				fseek($this->fp, $arItem['L']);
				$xml = fread($this->fp, $arItem['R'] - $arItem['L']);
				fseek($this->fp, $c);
			}
			return $xml;
		}

		/**
		 * Internal function.
		 * Used to read an xml by chunks started with "<" and endex with "<"
		 *
		 * @param resource $fp
		 * @return bool|string
		 */
		function _get_xml_chunk($fp)
		{
			if($this->buf_position >= $this->buf_len)
			{
				if(!feof($fp))
				{
					$this->buf = fread($fp, $this->read_size);
					$this->buf_position = 0;
					$this->buf_len = mb_strlen($this->buf, 'latin1');
				}
				else
					return false;
			}

			//Skip line delimiters (ltrim)
			$xml_position = mb_strpos($this->buf, "<", $this->buf_position, 'latin1');
			while($xml_position === $this->buf_position)
			{
				$this->buf_position++;
				$this->file_position++;
				//Buffer ended with white space so we can refill it
				if($this->buf_position >= $this->buf_len)
				{
					if(!feof($fp))
					{
						$this->buf = fread($fp, $this->read_size);
						$this->buf_position = 0;
						$this->buf_len = mb_strlen($this->buf, 'latin1');
					}
					else
						return false;
				}
				$xml_position = mb_strpos($this->buf, "<", $this->buf_position, 'latin1');
			}

			//Let's find next line delimiter
			while($xml_position===false)
			{
				$next_search = $this->buf_len;
				//Delimiter not in buffer so try to add more data to it
				if(!feof($fp))
				{
					$this->buf .= fread($fp, $this->read_size);
					$this->buf_len = mb_strlen($this->buf, 'latin1');
				}
				else
					break;

				//Let's find xml tag start
				$xml_position = mb_strpos($this->buf, "<", $next_search, 'latin1');
			}
			if($xml_position===false)
				$xml_position = $this->buf_len+1;

			$len = $xml_position-$this->buf_position;
			$this->file_position += $len;
			$result = mb_substr($this->buf, $this->buf_position, $len, 'latin1');
			$this->buf_position = $xml_position;

			return $result;
		}

		/*
		Internal function.
		Stores an element into xml database tree.
		*/
		function _start_element($xmlChunk)
		{
			static $search = array(
					"'&(quot|#34);'i",
					"'&(lt|#60);'i",
					"'&(gt|#62);'i",
					"'&(amp|#38);'i",
				);

			static $replace = array(
					"\"",
					"<",
					">",
					"&",
				);

			$p = mb_strpos($xmlChunk, ">", 0, 'latin1');
			if($p !== false)
			{
				if(mb_substr($xmlChunk, $p - 1, 1, 'latin1') == "/")
				{
					$bHaveChildren = false;
					$elementName = mb_substr($xmlChunk, 0, $p - 1, 'latin1');
					$DBelementValue = false;
				}
				else
				{
					$bHaveChildren = true;
					$elementName = mb_substr($xmlChunk, 0, $p, 'latin1');
					$elementValue = mb_substr($xmlChunk, $p + 1, null, 'latin1');
					if(preg_match("/^\s*$/", $elementValue))
						$DBelementValue = false;
					elseif(strpos($elementValue, "&") === false)
						$DBelementValue = $elementValue;
					else
						$DBelementValue = preg_replace($search, $replace, $elementValue);
				}

				if(($ps = strpos($elementName, " "))!==false)
				{
					//Let's handle attributes
					$elementAttrs = mb_substr($elementName, $ps + 1, null, 'latin1');
					$elementName = mb_substr($elementName, 0, $ps, 'latin1');
					preg_match_all("/(\\S+)\\s*=\\s*[\"](.*?)[\"]/s".BX_UTF_PCRE_MODIFIER, $elementAttrs, $attrs_tmp);
					$attrs = array();
					if(strpos($elementAttrs, "&") === false)
					{
						foreach($attrs_tmp[1] as $i=>$attrs_tmp_1)
							$attrs[$attrs_tmp_1] = $attrs_tmp[2][$i];
					}
					else
					{
						foreach($attrs_tmp[1] as $i=>$attrs_tmp_1)
							$attrs[$attrs_tmp_1] = preg_replace($search, $replace, $attrs_tmp[2][$i]);
					}
					$DBelementAttrs = $attrs;
				}
				else
					$DBelementAttrs = false;

				if($c = count($this->element_stack))
					$parent = $this->element_stack[$c-1];
				else
					$parent = array("ID"=>"NULL", "L"=>0, "R"=>1);

				$left = $parent["R"];
				$right = $left+1;

				$arFields = array(
					"PARENT_ID" => $parent["ID"],
					"LEFT_MARGIN" => $left,
					"RIGHT_MARGIN" => $right,
					"DEPTH_LEVEL" => $c,
					"NAME" => $elementName,
				);
				if($DBelementValue !== false)
				{
					$arFields["VALUE"] = $DBelementValue;
				}
				if($DBelementAttrs !== false)
				{
					$arFields["ATTRIBUTES"] = $DBelementAttrs;
				}

				$this->item = $arFields;

				if($bHaveChildren)
					$this->element_stack[] = array("ID"=>++$this->last_id, "L"=>$left, "R"=>$right, "RO"=>$right);
				else
					$this->element_stack[$c-1]["R"] = $right+1;
				
				/*if(isset($this->depth) && $this->depth > $c)
				{
					for($i=$c+1; $i<=$this->depth; $i++)
					{
						unset($this->depth_stack[$i]);
					}
				}*/
				$this->depth_stack[$c] = array(
					"N" => $elementName,
					"L" => $this->file_position - mb_strlen($xmlChunk, 'latin1') - 1
				);
				
				$this->name = $elementName;
				$this->depth = $c;
				$this->nodeType = self::ELEMENT;
				return true;
			}
			return false;
		}

		/*
		Internal function.
		Winds tree stack back. Modifies (if neccessary) internal tree structure.
		*/
		function _end_element($xmlChunk)
		{
			$child = array_pop($this->element_stack);
			$this->element_stack[count($this->element_stack)-1]["R"] = $child["R"]+1;
			
			$elementName = '';
			if(($p1 = mb_strpos($xmlChunk, '/', 0, 'latin1')) !== false && ($p2 = mb_strpos($xmlChunk, '>', 0, 'latin1')) !== false);
			$elementName = mb_substr($xmlChunk, $p1 + 1, $p2 - $p1 - 1, 'latin1');
			$i = count($this->depth_stack) - 1;
			while(isset($this->depth_stack[$i]) && isset($this->depth_stack[$i]['R']))
			{
				unset($this->depth_stack[$i]);
				$i--;
			}
			if($this->depth_stack[$i]['N']==$elementName)
			{
				$this->depth_stack[$i]['R'] = $this->file_position;
				$this->name = $elementName;
				$this->depth = $i;
				return true;
			}
			return false;
		}
	}
}
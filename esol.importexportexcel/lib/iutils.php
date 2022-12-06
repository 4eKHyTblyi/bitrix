<?php
namespace Bitrix\KdaImportexcel;

class IUtils
{
	public static $moduleId = 'esol.importexportexcel';
	public static $moduleSubDir = 'import/';
	
	public static function GetCurUserID()
	{
		global $USER;
		if($USER && is_callable(array($USER, 'GetID'))) return $USER->GetID();
		else return 0;
	}
	
	public static function Trim($str)
	{
		$str = trim($str);
		$str = preg_replace('/(^(\xC2\xA0|\s)+|(\xC2\xA0|\s)+$)/s', '', $str);
		return $str;
	}
	
	public static function Translate($string, $langFrom, $langTo=false)
	{
		if(strlen(trim($string)) == 0) return $string;
		if($apiKey = \Bitrix\Main\Config\Option::get(static::$moduleId, 'TRANSLATE_GOOGLE_KEY', ''))
		{
			if($langTo===false) $langTo = LANGUAGE_ID;
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
			$res = $client->post('https://translation.googleapis.com/language/translate/v2', array('q'=>$string, 'source'=>$langFrom, 'target'=>$langTo, 'format'=>"text", 'key'=>$apiKey));
			$arRes = \CUtil::JSObjectToPhp($res);
			if(isset($arRes['data']['translations'][0]['translatedText']))
			{
				$string = (is_array($arRes['data']['translations'][0]['translatedText']) ? implode('', $arRes['data']['translations'][0]['translatedText']) : $arRes['data']['translations'][0]['translatedText']);
			}
		}
		elseif(($apiKey = \Bitrix\Main\Config\Option::get('main', 'translate_key_yandex', '')) ||
			($apiKey = \Bitrix\Main\Config\Option::get(static::$moduleId, 'TRANSLATE_YANDEX_KEY', '')))
		{
			if($langTo===false) $langTo = LANGUAGE_ID;
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
			$client->setHeader('Content-Type', 'application/xml');
			$res = $client->get('https://translate.yandex.net/api/v1.5/tr.json/translate?key='.$apiKey.'&lang='.$langFrom.'-'.$langTo.'&text='.urlencode($string));
			$arRes = \CUtil::JSObjectToPhp($res);
			if(array_key_exists('code', $arRes) && $arRes['code']==200 && array_key_exists('text', $arRes))
			{
				$string = (is_array($arRes['text']) ? implode('', $arRes['text']) : $arRes['text']);
			}
		}
		return $string;
	}
	
	public static function Str2Url($string, $arParams=array())
	{
		if(!is_array($arParams)) $arParams = array();
		
		if(count($arParams)==0)
		{
			$arTransParams = \Bitrix\Main\Config\Option::get(static::$moduleId, 'TRANS_PARAMS', '');
			if(is_string($arTransParams) && !empty($arTransParams)) $arTransParams = unserialize($arTransParams);
			if(!is_array($arTransParams)) $arTransParams = array();
			if(!empty($arTransParams))
			{
				$arTransParams['TRANSLITERATION'] = 'Y';
				$arParams = $arTransParams;
			}
		}
		
		if($arParams['TRANSLITERATION']=='Y')
		{
			if($arParams['USE_GOOGLE']=='Y') $string = self::Translate($string, LANGUAGE_ID, 'en');

			if(isset($arParams['TRANS_LEN'])) $arParams['max_len'] = $arParams['TRANS_LEN'];
			if(isset($arParams['TRANS_CASE'])) $arParams['change_case'] = $arParams['TRANS_CASE'];
			if(isset($arParams['TRANS_SPACE'])) $arParams['replace_space'] = $arParams['TRANS_SPACE'];
			if(isset($arParams['TRANS_OTHER'])) $arParams['replace_other'] = $arParams['TRANS_OTHER'];
			if(isset($arParams['TRANS_EAT']) && $arParams['TRANS_EAT']=='N') $arParams['delete_repeat_replace'] = false;
		}
		return \CUtil::translit($string, LANGUAGE_ID, $arParams);
	}
	
	public static function DownloadTextTextByLink($val, $altVal='')
	{
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
		$path = (strlen(trim($altVal)) > 0 ? trim($altVal) : trim($val));
		if(strlen($path)==0) return '';
		$arUrl = parse_url($path);
		$res = trim($client->get($path));
		if($client->getStatus()==404) $res = '';
		$hct = ToLower($client->getHeaders()->get('content-type'));
		$siteEncoding = \CKDAImportUtils::getSiteEncoding();
		if(strlen($res) > 0 && class_exists('\DOMDocument') && $arUrl['fragment'])
		{
			$res = self::GetHtmlDomVal($res, $arUrl['fragment']);
		}
		elseif(preg_match('/charset=(.+)(;|$)/Uis', $hct, $m))
		{
			$fileEncoding = ToLower(trim($m[1]));
			if($siteEncoding!=$fileEncoding)
			{
				$res = \Bitrix\Main\Text\Encoding::convertEncoding($res, $fileEncoding, $siteEncoding);
			}
		}
		else
		{
			if(\CUtil::DetectUTF8($res))
			{
				if($siteEncoding!='utf-8') $res = \Bitrix\Main\Text\Encoding::convertEncoding($res, 'utf-8', $siteEncoding);
			}
			elseif($siteEncoding=='utf-8') $res = \Bitrix\Main\Text\Encoding::convertEncoding($res, 'cp1251', $siteEncoding);
		}
		return $res;
	}
	
	public static function GetHtmlDomVal($html, $selector, $img=false, $multi=false, $path=false)
	{
		$finalHtml = '';
		if(strlen($html) > 0 && class_exists('\DOMDocument') && $selector)
		{
			if($multi && !$img) $multi = false;
			/*Bom UTF-8*/
			if(\CUtil::DetectUTF8(substr($html, 0, 10000)) && (substr($html, 0, 3)!="\xEF\xBB\xBF"))
			{
				$html = "\xEF\xBB\xBF".$html;
			}
			/*/Bom UTF-8*/
			$doc = new \DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->loadHTML($html);
			$node = $doc;
			$arNodes = array();
			$arParts = preg_split('/\s+/', $selector);
			$i = 0;
			while(isset($arParts[$i]) && ($node instanceOf \DOMDocument || $node instanceOf \DOMElement))
			{
				$part = $arParts[$i];
				$tagName = (preg_match('/^([^#\.\[]+)([#\.\[].*$|$)/', $part, $m) ? $m[1] : '');
				$tagId = (preg_match('/^[^#]*#([^#\.\[]+)([#\.\[].*$|$)/', $part, $m) ? $m[1] : '');
				$arClasses = array_diff(explode('.', (preg_match('/^[^\.]*\.([^#\[]+)([#\.\[].*$|$)/', $part, $m) ? $m[1] : '')), array(''));
				$arAttributes = array_map(array(__CLASS__, 'GetDomAttributes'), (preg_match_all('/\[([^\]]+(=[^\]])?)\]/', $part, $m) ? $m[1] : array()));
				if($tagName)
				{
					$nodes = $node->getElementsByTagName($tagName);
					if($tagId || !empty($arClasses) || !empty($arAttributes))
					{
						$find = false;
						$key = 0;
						while((!$find || $multi) && $key<$nodes->length)
						{
							$node1 = $nodes->item($key);
							$subfind = true;
							if($tagId && $node1->getAttribute('id')!=$tagId) $subfind = false;
							foreach($arClasses as $className)
							{
								if($className && !preg_match('/(^|\s)'.preg_quote($className, '/').'(\s|$)/is', $node1->getAttribute('class'))) $subfind = false;
							}
							foreach($arAttributes as $arAttr)
							{
								if(!$node1->hasAttribute($arAttr['k']) || (strlen($arAttr['v']) > 0 && $node1->getAttribute($arAttr['k'])!=$arAttr['v'])) $subfind = false;
							}
							$find = $subfind;
							if($multi && $subfind) $arNodes[] = $nodes->item($key);
							if(!$find || $multi) $key++;
						}
						if($find && !$multi) $node = $nodes->item($key);
						else $node = null;
					}
					else
					{
						if($multi)
						{
							$key = 0;
							while($key<$nodes->length)
							{
								$arNodes[] = $nodes->item($key);
								$key++;
							}
						}
						$node = $nodes->item(0);
					}
				}
				$i++;
			}
			
			if($img && $multi && count($arNodes) > 0)
			{
				$arLinks = array();
				foreach($arNodes as $node)
				{
					if($node instanceOf \DOMElement)
					{
						$link = '';
						if($node->hasAttribute('src')) $link = $node->getAttribute('src');
						elseif($node->hasAttribute('href')) $link = $node->getAttribute('href');
						$link = trim($link);
						if(strlen($link) > 0 && !preg_match('#^https?://#', $link) && $path)
						{
							$arUrl = parse_url($path);
							$protocol = $arUrl['scheme'];
							$host = $protocol.'://'.$arUrl['host'];
							if(strpos($link, '/')===0) $link = $host.$link;
							else $link = $host.$arUrl['path'].$link;
						}
						if(strlen($link) > 0) $arLinks[] = $link;
					}
				}
				return $arLinks;
			}
			
			if($node instanceOf \DOMElement)
			{
				$innerHTML = '';
				if($img)
				{
					if($node->hasAttribute('src')) $innerHTML = $node->getAttribute('src');
					elseif($node->hasAttribute('href')) $innerHTML = $node->getAttribute('href');
					if(strlen($innerHTML) > 0 && !preg_match('#^https?://#', $innerHTML) && $path)
					{
						$arUrl = parse_url($path);
						$protocol = $arUrl['scheme'];
						$host = $protocol.'://'.$arUrl['host'];
						if(strpos($innerHTML, '/')===0) $innerHTML = $host.$innerHTML;
						else $innerHTML = $host.$arUrl['path'].$innerHTML;
					}
				}
				else
				{
					$children = $node->childNodes;
					foreach($children as $child)
					{
						$innerHTML .= $child->ownerDocument->saveHTML($child);
					}
					if(strlen($innerHTML)==0 && $node->nodeValue) $innerHTML = $node->nodeValue;
				}
				$finalHtml = trim($innerHTML);
			}
			else
			{
				$finalHtml = '';
			}
			$siteEncoding = \CKDAImportUtils::getSiteEncoding();
			if($finalHtml && $siteEncoding!='utf-8')
			{
				$finalHtml = \Bitrix\Main\Text\Encoding::convertEncoding($res, 'utf-8', $siteEncoding);
			}
		}
		return $finalHtml;
	}
	
	public static function DownloadImagesFromText($val, $domain='')
	{
		$domain = trim($domain);
		$imgDir = '/upload/esol_images/';
		$arPatterns = array(
			'/<img[^>]*\ssrc=["\']([^"\'<>]+)["\'][^>]*>/Uis',
			'/<a[^>]*\shref=["\']([^"\'<>]+\.(jpg|jpeg|png|gif|svg|webp|bmp|pdf)(\?[^"\']*)?)["\'][^>]*>/Uis',
		);
		foreach($arPatterns as $pattern)
		{
			if(preg_match_all($pattern, $val, $m))
			{
				foreach($m[1] as $k=>$img)
				{
					if(strpos($img, '//')===0) $img = (($pos = strpos($domain, '//'))!==false ? substr($domain, 0, $pos) : 'http:').$img;
					elseif(strpos($img, '/')===0) $img = $domain.$img;
					$imgName = md5($img).'.'.preg_replace('/[#\?].*$/', '', bx_basename(rawurldecode($img)));
					$imgPathDir1 = $imgDir.substr($imgName, 0, 3).'/';
					$imgPathDir = $_SERVER['DOCUMENT_ROOT'].$imgPathDir1;
					$imgPath1 = $imgPathDir1.$imgName;
					$imgPath = $imgPathDir.$imgName;
					$realFile = \Bitrix\Main\IO\Path::convertLogicalToPhysical($imgPath);
					if(!file_exists($realFile) || filesize($realFile)==0)
					{
						CheckDirPath($imgPathDir);
						$ob = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true, 'socketTimeout'=>15, 'streamTimeout'=>15));
						$ob->setHeader('User-Agent', \CKDAImportUtils::GetUserAgent());
						$ob->download($img, $imgPath);
					}
					$imgHtml = str_replace($m[1][$k], $imgPath1, $m[0][$k]);
					$val = str_replace($m[0][$k], $imgHtml, $val);
				}
			}
		}
		return $val;
	}
	
	public static function GetFloatRoundVal($val)
	{
		if(($ar = explode('.', $val)) && count($ar)>1){$val = round($val, strlen($ar[1]));}
		return $val;
	}
	
	public static function GetDomAttributes($n)
	{
		list($k,$v)=explode("=", $n, 2);
		return array("k"=>$k, "v"=>trim($v, " \"\'"));
	}
}
<?php
namespace Bitrix\KdaImportexcel;

class HttpClient extends \Bitrix\Main\Web\HttpClient
{
	protected static $mProxyParams = null;
	
	public function mInitProxyParams()
	{
		if(!isset(self::$mProxyParams))
		{
			$moduleId = IUtils::$moduleId;
			self::$mProxyParams = array(
				'proxyHost' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_HOST', ''),
				'proxyPort' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_PORT', ''),
				'proxyUser' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_USER', ''),
				'proxyPassword' => \Bitrix\Main\Config\Option::get($moduleId, 'PROXY_PASSWORD', ''),
			);
			if(!self::$mProxyParams['proxyHost'] || !self::$mProxyParams['proxyPort'])
			{
				self::$mProxyParams = array();
			}
		}
		if(!empty(self::$mProxyParams))
		{
			$p = self::$mProxyParams;
			$this->setProxy($p['proxyHost'], $p['proxyPort'], $p['proxyUser'], $p['proxyPassword']);
			return true;
		}
		return false;
	}
	
	public function download($url, $filePath)
	{
		if($this->mInitProxyParams() && preg_match('/^\s*https:/i', $url) && function_exists('curl_init'))
		{
			$p = self::$mProxyParams;
			$arOrigHeaders = $this->requestHeaders->toArray();
			$arHeaders = array();
			$arSHeaders = array();
			foreach($arOrigHeaders as $header)
			{
				foreach($header["values"] as $value)
				{
					$arHeaders[] = $header["name"] . ": ".$value;
					$arSHeaders[$header["name"]] =  $value;
				}
			}
			$filePath2 = \Bitrix\Main\IO\Path::convertPhysicalToLogical($filePath);
			$f = fopen($filePath2, 'w');
			$ch = curl_init();
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_PROXY, $p['proxyHost'].':'.$p['proxyPort']);
			if($p['proxyUser']) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $p['proxyUser'].':'.$p['proxyPassword']);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->redirect);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $arHeaders);
			if($arSHeaders['User-Agent']) curl_setopt($ch, CURLOPT_USERAGENT, $arSHeaders['User-Agent']);
			if($this->requestCookies->toString()) curl_setopt($ch, CURLOPT_COOKIE, $this->requestCookies->toString());
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->socketTimeout);
			curl_setopt($ch, CURLOPT_FILE, $f);
			curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'mCurlGetHeaders'));
			$res = curl_exec($ch);
			curl_close($ch);
			fclose($f);
			$this->status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
			return $res;			
		}
		
		return parent::download($url, $filePath);
	}
	
	public function mCurlGetHeaders($ch, $header)
	{
		$len = mb_strlen($header);
		$header = explode(':', $header, 2);
		if(count($header) < 2) return $len;

		$headerName = trim($header[0]);
		$headerValue = trim($header[1]);
		if(ToLower($headerName)=='set-cookie')
		{
			$this->responseCookies->addFromString($headerValue);
		}
		$this->responseHeaders->add($headerName, $headerValue);
		return $len;
	}
}

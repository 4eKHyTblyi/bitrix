<?php
IncludeModuleLangFile(__FILE__);

$storage = 'fs';
if(class_exists('\Bitrix\Main\Entity\DataManager'))
{
	$profileDB = new \Bitrix\KdaExportexcel\ProfileTable();
	$conn = $profileDB->getEntity()->getConnection();
	if($conn->getType()=='mysql')
	{
		$storage = 'db';
	}
}

if($storage=='db')
{
	class CKDAExportProfile extends CKDAExportProfileDB {}
	if(is_callable(array($conn, 'queryExecute')))
	{
		$conn->queryExecute('SET wait_timeout=900');
		$conn->queryExecute('SET sql_mode=""');
	}
}
else
{
	class CKDAExportProfile extends CKDAExportProfileFS {}
}

class CKDAExportProfileAll {
	protected static $instance = array();
	private $pid = null;
	private $errors = array();
	
	public static function getInstance($suffix='iblock')
	{
		if (!isset(static::$instance[$suffix]))
			static::$instance[$suffix] = new static($suffix=='iblock' ? '' : $suffix);

		return static::$instance[$suffix];
	}
	
	public function GetErrors()
	{
		if(!isset($this->errors) || !is_array($this->errors)) $this->errors = array();
		return implode('<br>', array_unique($this->errors));
	}

	public function ShowProfileList($fname)
	{
		$arProfiles = $this->GetList();
		?><select name="<?echo $fname;?>" id="<?echo $fname;?>" onchange="EProfile.Choose(this)" style="max-width: 350px;"><?
			?><option value=""><?echo GetMessage("KDA_EE_NO_PROFILE"); ?></option><?
			?><option value="new" <?if($_REQUEST[$fname]=='new'){echo 'selected';}?>><?echo GetMessage("KDA_EE_NEW_PROFILE"); ?></option><?
			foreach($arProfiles as $k=>$profile)
			{
				?><option value="<?echo $k;?>" <?if(strlen($_REQUEST[$fname])>0 && strval($_REQUEST[$fname])===strval($k)){echo 'selected';}?>><?echo $profile; ?></option><?
			}
		?></select><?
	}
	
	public function Apply(&$settigs_default, &$settings, $ID)
	{
		$arProfile = $this->GetByID($ID);
		if(!is_array($settigs_default) && is_array($arProfile['SETTINGS_DEFAULT']))
		{
			$settigs_default = $arProfile['SETTINGS_DEFAULT'];
		}
		if(!is_array($settings) && is_array($arProfile['SETTINGS']))
		{
			$settings = $arProfile['SETTINGS'];
		}
		if(is_array($settings))
		{
			if($settings['DISPLAY_PARAMS'])
			{
				foreach($settings['DISPLAY_PARAMS'] as $k=>$v)
				{
					if($v && !is_array($v))
					{
						$v = CUtil::JsObjectToPhp($v);
					}
					if(!is_array($v)) $v = array();
					$settings['DISPLAY_PARAMS'][$k] = $v;
				}
			}
		}
		
		if(!is_array($settigs_default)) $settigs_default = array();
		if(!is_array($settings)) $settings = array();
	}
	
	public function ApplyExtra(&$extrasettings, $ID)
	{
		$arProfile = $this->GetByID($ID);
		if(!is_array($extrasettings) && is_array($arProfile['EXTRASETTINGS']))
		{
			$extrasettings = $arProfile['EXTRASETTINGS'];
		}
	}
	
	public function UpdateFields($ID, $arFields)
	{
		return false;
	}
	
	public function GetLastImportProfiles($limit=10)
	{
		return array();
	}
	
	public function GetFieldsByID($ID)
	{
		return array();
	}
	
	public function GetStatus($id)
	{
		return '';
	}
	
	public function SetExportParams($pid)
	{
		$this->pid = $pid;
	}
	
	public function OnStartExport()
	{
		return  false;
	}
	
	public function OnEndExport($file, $arParams, $arErrors=array())
	{
		return array();
	}
	
	public function OutputBackup()
	{
		return false;
	}
	
	public function RestoreBackup($arFiles, $arParams)
	{
		return false;
	}
}
?>
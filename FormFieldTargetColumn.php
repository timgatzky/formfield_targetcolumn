<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		formfield_db_field
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

class FormFieldTargetColumn extends Backend
{
	/**
	 * Overwrite target columns
	 * @param array
	 * @param form
	 * called from storeFormData HOOK
	 */
	public function storeFormDataHook($arrData, $objForm)
	{
		// fetch form fields
		$objFormFields = $this->Database->prepare("SELECT id,type,name,setTargetColName,targetColName FROM tl_form_field WHERE pid=? AND setTargetColName=1")->execute($objForm->id);
		if($objFormFields->numRows < 1)
		{
			return;
		}
		
		// store field values of dublicate target fields
		$arrCombine = array();
		
		// overwrite the target fields (keys) in set/data array
		while($objFormFields->next())
		{
			$f_name = $objFormFields->name;
			$f_type = $objFormFields->type;
			
			$value = '';
			if($objFormFields->targetColName != $f_name)
			{
				$value = $arrData[$f_name];
				if(!empty($value) || strlen($value))
				{
					$value = $this->formatValue($f_type,$value,$GLOBALS['TL_CONFIG']['dateFormat']);
				}
				$newKey = $objFormFields->targetColName;
		
				// set new key
				$arrData[$newKey] = $value;
			
				// collect new values
				if($GLOBALS['FORMFIELD_DB_FIELD']['combine'])
				{
					$arrCombine[$newKey][] = $value;
				}
			
				// unset old key
				unset($arrData[$f_name]);
			}
			
		}
		
		// combine values
		if(count($arrCombine) > 0)
		{
			foreach($arrCombine as $field => $arrValues)
			{
				$arrData[$field] = implode($GLOBALS['FORMFIELD_DB_FIELD']['seperator'], $arrValues);
			}
		}
		
		return $arrData;
	}
	
	
	/**
	 * Format values by form field type
	 * @param string
	 * @param mixed
	 * @return mixed
	 */ 
	protected function formatValue($strType,$varValue,$strFormat='')
	{
		switch($strType)
		{
			case 'date':
			case 'calendar':
				$this->import('Date');
				
				$objDate = new Date($varValue,$strFormat);
				
				$varValue = $objDate->tstamp;
				
				break;
			
			default:
				// HOOK allow other extensions to modify the form field value
				if (isset($GLOBALS['TL_HOOKS']['storeFormData']['formatValue']) && count($GLOBALS['TL_HOOKS']['storeFormData']['formatValue']) > 0)
				{
					foreach($GLOBALS['TL_HOOKS']['storeFormData']['formatValue'] as $callback)
					{
						$this->import($callback[0]);
						$varValue = $this->$callback[0]->$callback[1]($strType,$varValue,$strFormat);
					}
				}
			break;
		}
		
		return $varValue;
	}
}
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @copyright Tim Gatzky 2013
 * @author  Tim Gatzky <info@tim-gatzky.de>
 * @package  formfield_db_field
 * @link  http://contao.org
 * @license  http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


$GLOBALS['TL_DCA']['tl_form_field']['config']['onload_callback'][] = array('tl_form_field_form_db_field', 'modifyDCA');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'setTargetColName';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['setTargetColName'] = 'targetColName';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['setTargetColName'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['setTargetColName'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'       			  => array('submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['targetColName'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['targetColName'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'    	  => array('tl_form_field_form_db_field','getFields'),
	'eval'       			  => array('insertBlankOption'=>true)
);


class tl_form_field_form_db_field extends Backend
{
	/**
	 * Modify the DCA
	 *
	 * Only initiate the field when the form saves its data to a table
	 * @param object
	 */
	public function modifyDCA(DataContainer $dc)
	{
		$objForm = $this->Database->prepare("SELECT * FROM tl_form WHERE id=(SELECT pid from tl_form_field WHERE id=?)")
		->limit(1)
		->execute($dc->id);
			
		if($objForm->storeValues)
		{
			/**
			 * Palettes
			 */
			foreach($GLOBALS['TL_DCA']['tl_form_field']['palettes'] as $type => $palette)
			{
				if($type == '__selector__' || $type == 'default' || $type == 'submit')
				{
				   continue;
				}

				$palette .= ';{form_db_field_legend:hide},setTargetColName;';
				$GLOBALS['TL_DCA']['tl_form_field']['palettes'][$type] = $palette;
			}
		}
		
	}


	/**
	 * Get the fields of a table and return as array
	 * @param object
	 * @return array
	 */
	public function getFields(DataContainer $dc)
	{
		$arrReturn = array();

		// fetch form settings
		$objForm = $this->Database->prepare("SELECT * FROM tl_form WHERE id=(SELECT pid from tl_form_field WHERE id=?)")
		->limit(1)
		->execute($dc->id);
		
		if(!strlen($objForm->targetTable) || !$objForm->storeValues)
		{
			return '';
		}
		
		// check if tbale exists and has fields
		if(!$this->Database->tableExists($objForm->targetTable) || count($this->Database->getFieldNames($objForm->targetTable)) < 1 )
		{
			return '';
		}
		
		// fetch fields of target table
		$arrReturn = $this->Database->getFieldNames($objForm->targetTable);				
		
		// fix #1: ignore PRIMARY in list. Added global to add more fields to the ignore list
		if(count($arrReturn) > 0)
		{
			foreach($arrReturn as $i => $field)
			{
				if(in_array($field, $GLOBALS['FORMFIELD_DB_FIELD']['ignore']))
				{
					unset($arrReturn[$i]);
				}
			}
		}
		
		// order
		sort($arrReturn);
		
		return $arrReturn;
	}


}
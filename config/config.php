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

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['storeFormData'][] 		= array('FormFieldTargetColumn', 'storeFormDataHook');

/**
 * Globals
 */
$GLOBALS['FORMFIELD_DB_FIELD']['combine'] 		= true; 	// combine values of different fields with the same target column
$GLOBALS['FORMFIELD_DB_FIELD']['seperator'] 	= ','; 		// seperate combined values with this character
$GLOBALS['FORMFIELD_DB_FIELD']['ignore']		= array('PRIMARY'); // add fields to be ignored in the list
<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  CyberSpectrum 2011
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    CatalogContentElement
 * @license    LGPL
 * @filesource
 */

// we reuse namings etc from tl_module for table fields.
$this->loadDataContainer('tl_module');


$GLOBALS['TL_DCA']['tl_catalog_ce'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'				=> 'Table',
		'ptable'					=> 'tl_catalog_types',
		'switchToEdit'				=> true,
		'enableVersioning'			=> true,
		'onload_callback'			=> array
		(
			// TODO: add permission checks in future
			// array('tl_catalog_ce', 'checkPermission'),
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'					=> 1,
			'fields'				=> array('name'),
			'flag'					=> 1,
			'panelLayout'			=> 'filter;search,limit'
		),

		'label' => array
		(
			'fields'				=> array('name'),
			'format'				=> '%s',
//			'label_callback'		=> array('tl_catalog_ce','getRowLabel')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
		),

		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_ce']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_ce']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				'button_callback'     => array('tl_catalog_ce', 'getBtn')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_ce']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'button_callback'     => array('tl_catalog_ce', 'getBtn'),
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_catalog_ce']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
		)
	),
	// Palettes
	'palettes' => array
	(
		'default'         => '{title_legend},name,catalog_display_ce;{config_legend},catalog,catalog_visible,catalog_goback_disable,catalog_comments_disable;{template_legend:hide},catalog_template,catalog_layout;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space',
	),

	'fields' => array
	(
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['name'],
			'exclude'                 => true,
			'inputType'               => 'text',
		),

		'catalog_display_ce' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['catalog_display_ce'],
			'default'                 => 'catalog_full',
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_catalog_ce','getCatalogFields'),
			'eval'                    => array('tl_class'=>'w50')
		),

		'catalog' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['catalog'],
			'exclude'                 => true,
			'inputType'               => 'radio',
			'foreignKey'              => 'tl_catalog_types.name',
			'eval'                    => array('mandatory'=> true, 'submitOnChange'=> true)
		),

		'catalog_template' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['catalog_template'],
			'default'                 => 'catalog_full',
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_catalog_ce','getCatalogTemplates'),
			'eval'                    => array('tl_class'=>'w50')
		),

		'catalog_layout' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['catalog_layout'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_catalog_ce', 'getContentElementTemplates'),
			'eval'                    => array('tl_class'=>'w50')
		),

		'catalog_visible' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_catalog_ce']['catalog_visible'],
			'exclude'                 => true,
			'inputType'               => 'checkboxWizard',
			'options_callback'        => array('tl_catalog_ce', 'getCatalogFields'),
			'eval'                    => array('multiple'=> true, 'mandatory'=> true)
		),
	)

);


/**
 * Class tl_catalog_ce
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright	CyberSpectrum 2012
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    Controller
 */
class tl_catalog_ce extends Backend
{

	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Return the copy archive button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getBtn($row, $href, $label, $title, $icon, $attributes)
	{
		if (!$this->User->isAdmin)
		{
			return '';
		}
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}


	/**
	 * Get all catalog fields and return them as array
	 * @return array
	 */
	public function getCatalogFields(DataContainer $dc, $arrTypes=false, $blnImage=false)
	{
		if(!$arrTypes)
			$arrTypes=$GLOBALS['BE_MOD']['content']['catalog']['typesCatalogFields'];
		$fields = array();
		$chkImage = $blnImage ? " AND c.showImage=1" : "";
		$objFields = $this->Database->prepare("SELECT c.* FROM tl_catalog_fields c, tl_catalog_ce m WHERE c.pid=m.catalog AND m.id=? AND c.type IN ('" . implode("','", $arrTypes) . "')".$chkImage." ORDER BY c.sorting ASC")
							->execute($this->Input->get('id'));
		while ($objFields->next())
		{
			$value = strlen($objFields->name) ? $objFields->name.' ' : '';
			$value .= '['.$objFields->colName.':'.$objFields->type.']';
			$fields[$objFields->colName] = $value;
		}
		return $fields;
	}

	public function getContentElementTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('ce_catalog');
	}

	public function getCatalogTemplates(DataContainer $dc)
	{
		// fix issue #70 - template selector shall only show relevant templates.
		if (version_compare(VERSION.'.'.BUILD, '2.9.0', '>='))
		{
			return $this->getTemplateGroup('catalog_', $dc->activeRecord->pid);
		}
		else
		{
			return $this->getTemplateGroup('catalog_');
		}
	}
	
}

?>
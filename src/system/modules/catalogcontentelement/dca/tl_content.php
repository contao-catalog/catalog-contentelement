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

/**
 * Add palette to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['catalog'] = '{type_legend},type,headline;{catalog_legend},cat_cetemplate,cat_item;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_content']['fields']['cat_cetemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cat_cetemplate'],
	'default'                 => 'com_default',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_catalog', 'getTemplates'),
	'eval'                    => array(
		'submitOnChange' => true,
		'includeBlankOption' => true
	),
	'wizard' => array
	(
		array('tl_content_catalog', 'editTemplate')
	)
);


$GLOBALS['TL_DCA']['tl_content']['fields']['cat_item'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cat_item'],
	'default'                 => 'com_default',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_catalog', 'getItems')
);


class tl_content_catalog extends Backend
{
	public function getTemplates(DataContainer $dc)
	{
		$objTemplates=$this->Database->prepare('SELECT * FROM tl_catalog_ce')
									->execute();
		$arrTemplates=array();
		while($objTemplates->next())
		{
			$arrTemplates[$objTemplates->id]=$objTemplates->name;
		}
		return $arrTemplates;
	}

	public function editTemplate(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=catalog&amp;table=tl_catalog_ce&amp;id=' . $dc->value . '&amp;act=edit" title="'.sprintf(specialchars($GLOBALS['TL_LANG']['tl_content']['editcatalogce'][1]), $dc->value).'">' . $this->generateImage('alias.gif', $GLOBALS['TL_LANG']['tl_content']['editcatalogce'][0], 'style="vertical-align:top;"') . '</a>';
	}

	public function getItems(DataContainer $dc)
	{
		if($dc->activeRecord->cat_cetemplate)
		{
			$objCatalogModule=new CatalogContentElement($dc->activeRecord);
			return $objCatalogModule->generateList();
		}
	}
}




?>
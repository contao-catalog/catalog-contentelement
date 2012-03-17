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

class CatalogContentElementModule extends ModuleCatalogReader
{

	public function __construct(Database_Result $objModule, $strColumn='main')
	{
		parent::__construct($objModule, $strColumn);

		// get DCA
		$objCatalog = $this->Database->prepare('SELECT c.*, ce.catalog_visible, ce.catalog_template, ce.catalog_display_ce FROM tl_catalog_types c LEFT JOIN tl_catalog_ce ce ON (ce.pid=c.id) WHERE ce.id=?')
				->limit(1)
				->execute($this->cat_cetemplate);
		
		$this->objCatalogType = $objCatalog;
		
		if ($objCatalog->numRows > 0 && $objCatalog->tableName)
		{
			$this->catalog = $objCatalog->id;
			
			// dynamically load dca for catalog operations
			$this->Import('Catalog');
			if(!$GLOBALS['TL_DCA'][$objCatalog->tableName]['Cataloggenerated'])
			{
				// load language files and DC.
				$this->loadLanguageFile($objCatalog->tableName);
				$this->loadDataContainer($objCatalog->tableName);
				
				// load default language
				$GLOBALS['TL_LANG'][$objType->tableName] = is_array($GLOBALS['TL_LANG'][$objType->tableName])
													 ? Catalog::array_replace_recursive($GLOBALS['TL_LANG']['tl_catalog_items'], $GLOBALS['TL_LANG'][$objType->tableName])
													 : $GLOBALS['TL_LANG']['tl_catalog_items'];
				// load dca
				$GLOBALS['TL_DCA'][$objCatalog->tableName] = 
					is_array($GLOBALS['TL_DCA'][$objCatalog->tableName])
						? Catalog::array_replace_recursive($this->Catalog->getCatalogDca($this->catalog), $GLOBALS['TL_DCA'][$objCatalog->tableName])
						: $this->Catalog->getCatalogDca($this->catalog);
				$GLOBALS['TL_DCA'][$objCatalog->tableName]['Cataloggenerated'] = true;
			}
			$this->catalog_visible = deserialize($objCatalog->catalog_visible);
			$this->catalog_template = $objCatalog->catalog_template;
			$this->catalog_display_ce = $objCatalog->catalog_display_ce;
		}
	}
	
	public function generateList()
	{
		$colSort = implode('', $this->processFieldSQL(array($this->catalog_display_ce),
																									$this->catalog,
																									$this->objCatalogType->tableName));
		
		$arrConverted = $this->processFieldSQL($this->catalog_visible,
																						$this->catalog,
																						$this->objCatalogType->tableName);
		
		// might be calculated field.
		if(($p=strpos($colSort, 'AS')) !== false)
		{
			$strSort = substr($colSort, 0, $p);
		} else {
			$strSort = $colSort;
		}
		
		$objItems=$this->Database->prepare('SELECT '.implode(',',$this->systemColumns).','.implode(',',$arrConverted).', '.$colSort.', (SELECT name FROM tl_catalog_types WHERE tl_catalog_types.id='.$this->strTable.'.pid) AS catalog_name FROM '.$this->strTable. ' ORDER BY '.$strSort)
								->execute();
		
		foreach($this->generateCatalog($objItems, false, $arrFields, false) as $arrItem)
		{
			$arrItems[$arrItem['id']] = $arrItem['data'][$this->catalog_display_ce]['value'];
		}
		
		return $arrItems;
	}

	public function generateItem()
	{
		$arrConverted = $this->processFieldSQL($this->catalog_visible, $this->catalog,
																						$this->objCatalogType->tableName);
		
		$objItem=$this->Database->prepare('SELECT '.implode(',',$this->systemColumns).','.implode(',',$arrConverted).', (SELECT name FROM tl_catalog_types WHERE tl_catalog_types.id='.$this->strTable.'.pid) AS catalog_name FROM '.$this->strTable.' WHERE id=?')
								->execute($this->cat_item);
		
		return $this->parseCatalog($objItem, false, $this->catalog_template, $this->catalog_visible);
	}
}

class CatalogContentElement extends ContentElement
{
	protected $strTemplate = 'ce_catalog';

	public function __construct(Database_Result $objModule)
	{
		parent::__construct($objModule);
		$this->objModule = new CatalogContentElementModule($objModule);
	}

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### CATALOG ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = '';
			$objTemplate->href = '';
			if($this->cat_item)
			{
				$objTemplate->wildcard = sprintf('<div class="ce_catalog">%s</div>', $this->objModule->generateItem());
			}
			return $objTemplate->parse();
		}
		return parent::generate();
	}

	public function compile()
	{
		$this->Template->catalog=$this->objModule->generateItem();
		return;
	}

	public function generateList()
	{
		return $this->objModule->generateList();
	}
}

?>
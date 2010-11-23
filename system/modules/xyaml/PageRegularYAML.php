<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2009-2010 Leo Feyer
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
 * @copyright  InfinityLabs - Olck & Lins GbR 2009 
 * @author     Tristan Lins <tristan.lins@infinitylabs.de>
 * @package    xYAML 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class PageRegularYAML
 *
 * Provide methods to handle a regular front end page.
 * @copyright  InfinityLabs - Olck & Lins GbR 2009 
 * @author     Tristan Lins <tristan.lins@infinitylabs.de>
 * @package    xYAML 
 */
class PageRegularYAML extends PageRegular
{
	protected function getSelector($name) {
		if (is_array($GLOBALS['xYAML'][$name])) {
			return implode(', ', $GLOBALS['xYAML'][$name]);
		} else {
			return $GLOBALS['xYAML'][$name];
		}
	}
	/**
	 * Create a new template
	 * @param object
	 * @param object
	 */
	protected function createTemplate(Database_Result $objPage, Database_Result $objLayout)
	{
		// HOOK: modify the page or layout object
		if (isset($GLOBALS['TL_HOOKS']['createTemplate']) && is_array($GLOBALS['TL_HOOKS']['createTemplate']))
		{
			foreach ($GLOBALS['TL_HOOKS']['createTemplate'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($objPage, $objLayout, $this);
			}
		}
		
		parent::createTemplate($objPage, $objLayout);
		$this->Template->framework = '';
		
		// Initialize margin
		$arrMargin = array
		(
			'left'   => '0 auto 0 0',
			'center' => '0 auto',
			'right'  => '0 0 0 auto'
		);

		$strFramework = '';

		// Wrapper
		if ($objLayout->static)
		{
			$arrSize = deserialize($objLayout->width);
			$strFramework .= sprintf('%s { width:%s; margin:%s; }', $this->getSelector('wrapper_css_selector'), $arrSize['value'] . $arrSize['unit'], $arrMargin[$objLayout->align]) . "\n";
		}

		// Header
		if ($objLayout->header)
		{
			$arrSize = deserialize($objLayout->headerHeight);

			if ($arrSize['value'] > 0)
			{
				$strFramework .= sprintf('%s { height:%s; }', $this->getSelector('header_css_selector'), $arrSize['value'] . $arrSize['unit']) . "\n";
			}
		}

		$strMain = '';

		$mainMarginLeft = 0;
		$mainMarginRight = 0;
		
		// Left column
		if ($objLayout->cols == '2cll' || $objLayout->cols == '3cl')
		{
			$arrSize = deserialize($objLayout->widthLeft);

			if ($arrSize['value'] > 0)
			{
				$mainMarginLeft = $arrSize['value'] . $arrSize['unit'];
			}
		}

		// Right column
		if ($objLayout->cols == '2clr' || $objLayout->cols == '3cl')
		{
			$arrSize = deserialize($objLayout->widthRight);

			if ($arrSize['value'] > 0)
			{
				$mainMarginRight = $arrSize['value'] . $arrSize['unit'];
			}
		}

		// Main column
		$strFramework .= sprintf('%3$s { width: %1$s; }
%4$s { width: %2$s; }
%5$s { margin-left: %1$s; margin-right: %2$s; }
', $mainMarginLeft, $mainMarginRight,
		$this->getSelector('left_css_selector'),
		$this->getSelector('main_css_selector'),
		$this->getSelector('right_css_selector'));
		
		// Footer
		if ($objLayout->footer)
		{
			$arrSize = deserialize($objLayout->footerHeight);

			if ($arrSize['value'] > 0)
			{
				$strFramework .= sprintf('%s { height:%s; }', $this->getSelector('footer_css_selector'), $arrSize['value'] . $arrSize['unit']) . "\n";
			}
		}

		// Include basic style sheets
		$this->Template->framework .= sprintf('<link rel="stylesheet" href="%s/core/base.css" type="text/css" />',
			$GLOBALS['xYAML']['yaml_path'])."\n";
		
		// Add layout specific CSS
		$this->Template->framework .= '<style type="text/css" media="screen">' . "\n";
		$this->Template->framework .= '<!--/*--><![CDATA[/*><!--*/' . "\n";
		$this->Template->framework .= $strFramework;
		$this->Template->framework .= '/*]]>*/-->' . "\n";
		$this->Template->framework .= '</style>' . "\n";
		
		// Include additional style sheets
		foreach ($GLOBALS['xYAML']['css'] as $css) {
			$this->Template->framework .= sprintf('<link rel="stylesheet" href="%s" type="text/css" />', $css)."\n";
		}
		$this->Template->framework .= sprintf('<!--[if lte IE 7]><link rel="stylesheet" href="%s/core/iehacks.css" type="text/css" /><![endif]-->',
			$GLOBALS['xYAML']['yaml_path'])."\n";
		for ($i=6; $i<=8; $i++) {
			foreach ($GLOBALS['xYAML']['ie'.$i.'css'] as $css) {
				$this->Template->framework .= sprintf('<!--[if IE %d]><link rel="stylesheet" href="%s" type="text/css" /><![endif]-->', $i, $css)."\n";
			}
		}
	}
}

?>
<?php/*// K2 Multiple Images plugin by Andrey M// molotow11@gmail.com*/// no direct accessdefined('_JEXEC') or die('Restricted access');jimport('joomla.plugin.plugin');jimport('joomla.html.parameter');jimport('joomla.version');class plgSystemK2MultiImages extends JPlugin {	function plgSystemK2MultiImages($subject, $config) {		parent::__construct($subject, $config);	}		function onAfterRoute() {			error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING);			$version = new JVersion;		$joomlaVersion = $version->RELEASE;						$plugin = JPluginHelper::getPlugin('system', 'k2multiimages');		$plgParams = class_exists('JParameter') ? new JParameter($plugin->params) : new JRegistry($plugin->params);				$template = JRequest::getVar("template_name");				if($template == '') {			$template = $plgParams->get("template", "standart");		}				if($joomlaVersion < 1.6) {			$pluginPath = JPATH_SITE.DS.'plugins'.DS.'system'.DS.'K2MultiImages';			$pluginPathSite = JURI::root().'plugins/system/K2MultiImages';		}		else {			$pluginPath = JPATH_SITE.DS.'plugins'.DS.'system'.DS.'k2multiimages'.DS.'K2MultiImages';			$pluginPathSite = JURI::root().'plugins/system/k2multiimages/K2MultiImages';		}						//compatibility with K2Multirating			if(JPluginHelper::isEnabled('system', 'k2multirate')) {				$plgMultirate = JPluginHelper::getPlugin('system', 'k2multirate');				$plgMultirateParams = class_exists('JParameter') ? new JParameter($plgMultirate->params) : new JRegistry($plgMultirate->params);								if($plgMultirateParams->get("catfilter", 0) == 1) {					$selected = $plgMultirateParams->get("category_id", 1);										if(is_array($selected))						$selected = implode("|", $selected);										$catids = explode("|", $selected);										@require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');					$model = new K2ModelItemlist;										foreach($catids as $catid) {						if($plgMultirateParams->get("getChildren", 1)) {							$childs = K2ModelItemlist::getCategoryTree($catid);							foreach($childs as $child) {								$results[] = $child;							}						}						$results[] = $catid;					}										$itemID = JRequest::getInt("id");					$catid = $this->getCatid($itemID);										if(in_array($catid, $results)) {						$multirating = 1;					}					else {						$multirating = 0;					}				}				else {					$multirating = 1;				}			}			else {				$multirating = 0;			}			$mainframe = JFactory::getApplication();				if(JRequest::getVar("option") == "com_k2" && JRequest::getVar("view") == "item") {						//save and edit item			$task = JRequest::getVar("task");			if($task == "save" || $task == "apply" || $task == "saveAndNew") {										if (!defined('JPATH_ROOT')) {				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));				}										if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');							JRequest::checkToken() or jexit('Invalid Token');				require_once ($pluginPath.DS.'administrator'.DS.'models'.DS.'itemmultiimages.php');				$model = new K2ModelItemMultiImages;									if(!$mainframe->isAdmin()) {					$model->save(true);					}				else {					$model->save();				}								return;			}							//backend view			if ($mainframe->isAdmin()) { 										if (!defined('JPATH_ROOT')) {				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));				}										if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');				//replacing standart view				require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'item.php');				$controller = new K2ControllerItem;								$config['name'] =  "item";				$config['default_task'] =  "display";				$config['base_path'] =  $pluginPath.DS."administrator";				$config['model_path'] = $pluginPath.DS."administrator".DS."models";				$config['view_path'] =  $pluginPath.DS."administrator".DS."views";										$controller->__construct($config);								$view = $controller->getView("item", "html");								$view->addTemplatePath($pluginPath.DS."administrator".DS.'templates');				$controller->addModelPath($pluginPath.DS."administrator".DS."models");							}						//site item view						else if(JRequest::getVar("task") != "add" && JRequest::getVar("task") != "edit" && $multirating != 1 && $task != "vote" && $task != "getVotesPercentage" && $task != "getVotesNum" && $task != "getRatingsAverage") {								if (!defined('JPATH_ROOT')) {				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));				}										if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');				//replacing standart view				require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'item.php');				$controller = new K2ControllerItem;								$config['name'] =  "item";				$config['default_task'] =  "display";				$config['base_path'] =  $pluginPath;				$config['model_path'] = $pluginPath.DS."models";				$config['view_path'] =  $pluginPath.DS."views";										$controller->__construct($config);								$format = JRequest::getVar("format");				switch($format) {					case "raw" :						$view = $controller->getView("item", "raw");					break;											case "json" :						$view = $controller->getView("item", "json");					break;											case "html" :						$view = $controller->getView("item", "html");					break;																	default :						$view = $controller->getView("item", "html");					break;				}								$view->addTemplatePath($pluginPath.DS.'templates');								$multiimagesLang = JFactory::getLanguage();				$multiimagesLang->load("plg_system_k2multiimages", JPATH_ADMINISTRATOR);								$document = JFactory::getDocument();								switch($template) {									case "standart" :					case "slider-hs" :						$document->addScript($pluginPathSite.'/assets/highslide/highslide-full.js');						$document->addStyleSheet($pluginPathSite.'/assets/highslide/highslide.css');												$document->addScriptDeclaration("													if (typeof jQuery == 'undefined') {								document.write('<scr'+'ipt type=\"text/javascript\" src=\"".$pluginPathSite."/assets/jquery-1.7.1.min.js\"></scr'+'ipt>');								document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');							}													hs.graphicsDir = '".$pluginPathSite."/assets/highslide/graphics/';							hs.captionEval = 'this.a.rel';							hs.allowSizeReduction = true;							hs.align = 'center';							hs.zIndexCounter = '10001';							var galleryOptions = {								slideshowGroup: 'gallery',								wrapperClassName: 'dark',								dimmingOpacity: 0.7,								align: 'center',								transitions: ['expand', 'crossfade'],								fadeInOut: true,								wrapperClassName: 'borderless floating-caption',								marginLeft: 100,								marginBottom: 80,								numberPosition: 'caption'							};														// Add the slideshow controller							hs.addSlideshow({								slideshowGroup: 'gallery',								interval: 5000,								repeat: false,								useControls: true,								fixedControls: 'fit',								overlayOptions: {									className: 'text-controls',									opacity: 0.75,									position: 'bottom center',									offsetX: 0,									offsetY: -15,									hideOnMouseOut: true								}							});														//language							hs.lang = {																loadingText : '".JText::_('PLG_K2_MULTIIMAGES_LOADING')."',								nextText : '".JText::_('PLG_K2_MULTIIMAGES_NEXT')."', 								nextTitle : '".JText::_('PLG_K2_MULTIIMAGES_NEXT_TITLE')."',								closeText : '".JText::_('PLG_K2_MULTIIMAGES_CLOSE')."', 								closeTitle : '".JText::_('PLG_K2_MULTIIMAGES_CLOSE_TITLE')."', 								playText : '".JText::_('PLG_K2_MULTIIMAGES_PLAY')."',								playTitle : '".JText::_('PLG_K2_MULTIIMAGES_PLAY_TITLE')."',								pauseText : '".JText::_('PLG_K2_MULTIIMAGES_PAUSE')."',								pauseTitle : '".JText::_('PLG_K2_MULTIIMAGES_PAUSE_TITLE')."',								previousText : '".JText::_('PLG_K2_MULTIIMAGES_PREVIOUS')."',								previousTitle : '".JText::_('PLG_K2_MULTIIMAGES_PREVIOUS_TITLE')."',								fullExpandText : '".JText::_('PLG_K2_MULTIIMAGES_EXPAND')."',								fullExpandTitle : '".JText::_('PLG_K2_MULTIIMAGES_EXPAND_TITLE')."',								number: '".JText::_('PLG_K2_MULTIIMAGES_IMAGE')." %1 ".JText::_('PLG_K2_MULTIIMAGES_IMAGE_OF')." %2',								restoreTitle : '".JText::_('PLG_K2_MULTIIMAGES_RESTORE_TITLE')."'							}														");						break;				}			}						//add new item or edit from frontend			if(JRequest::getVar("task") == "add" || JRequest::getVar("task") == "edit") {										if (!defined('JPATH_ROOT')) {				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));				}										if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');							//replacing standart view				require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'item.php');				$controller = new K2ControllerItem;								$config['name'] =  "item";				$config['default_task'] =  "display";				$config['base_path'] =  $pluginPath.DS."administrator";				$config['model_path'] = $pluginPath.DS."administrator".DS."models";				$config['view_path'] =  $pluginPath.DS."administrator".DS."views";										$controller->__construct($config);								$view = $controller->getView("item", "html");								$view->addTemplatePath($pluginPath.DS."administrator".DS.'templates');				$controller->addModelPath($pluginPath.DS."administrator".DS."models");											}							}				if(JRequest::getVar("option") == "com_k2" && JRequest::getVar("view") == "itemlist" && !$mainframe->isAdmin() && JRequest::getVar("task") != "filter") {			//itemlist view				$document = JFactory::getDocument();								$document->addScriptDeclaration("											if (typeof jQuery == 'undefined') {						document.write('<scr'+'ipt type=\"text/javascript\" src=\"".$pluginPathSite."/assets/jquery-1.7.1.min.js\"></scr'+'ipt>');						document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');					}										jQuery(document).ready(function() {						jQuery('div.itemList .catItemImage').each(function() {							var title = jQuery(this).find('a').attr('title').split('|')[0];							if(title == '') {								var default_title = jQuery(this).parents('div.itemContainer').find('div.catItemHeader a:eq(0)').text().trim();								jQuery(this).find('a').attr('title', default_title);							}							else {								jQuery(this).find('a').attr('title', title);							}							var alt = jQuery(this).find('img').attr('alt').split('|')[0];							if(alt == '') {								var default_alt = jQuery(this).parents('div.itemContainer').find('div.catItemHeader a:eq(0)').text().trim();								jQuery(this).find('img').attr('alt', default_alt);							}							else {								jQuery(this).find('img').attr('alt', alt);							}						});						jQuery('div.genericItemList .genericItemImage').each(function() {							var title = jQuery(this).find('a').attr('title').split('|')[0];							if(title == '') {								var default_title = jQuery(this).parents('div.genericItemView').find('div.genericItemHeader a:eq(0)').text().trim();								jQuery(this).find('a').attr('title', default_title);							}							else {								jQuery(this).find('a').attr('title', title);							}							var alt = jQuery(this).find('img').attr('alt').split('|')[0];							if(alt == '') {								var default_alt = jQuery(this).parents('div.genericItemView').find('div.genericItemHeader a:eq(0)').text().trim();								jQuery(this).find('img').attr('alt', default_alt);							}							else {								jQuery(this).find('img').attr('alt', alt);							}						});												jQuery('div.userItemList .userItemImage').each(function() {							var title = jQuery(this).find('a').attr('title').split('|')[0];							if(title == '') {								var default_title = jQuery(this).parents('div.userItemView').find('div.userItemHeader a:eq(0)').text().trim();								jQuery(this).find('a').attr('title', default_title);							}							else {								jQuery(this).find('a').attr('title', title);							}							var alt = jQuery(this).find('img').attr('alt').split('|')[0];							if(alt == '') {								var default_alt = jQuery(this).parents('div.userItemView').find('div.userItemHeader a:eq(0)').text().trim();								jQuery(this).find('img').attr('alt', default_alt);							}							else {								jQuery(this).find('img').attr('alt', alt);							}						});					});									");						//k2multirate compatibility			if($multirating != 1) {							$format = JRequest::getVar('format');							if (!defined('JPATH_ROOT')) {				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));				}										if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');							require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'itemlist.php');				$controller = new K2ControllerItemlist;													$config['name'] =  "itemlist";				$config['default_task'] =  "display";				$config['base_path'] =  $pluginPath;				$config['model_path'] =  $pluginPath.DS."models";				$config['view_path'] =  $pluginPath.DS."views";									$controller->__construct($config);									switch($format) {					case "json" :						$view = $controller->getView("itemlist", "json");					break;										case "feed" :						$view = $controller->getView("itemlist", "feed");					break;										case "raw" :						$view = $controller->getView("itemlist", "raw");					break;										case "html" :						$view = $controller->getView("itemlist", "html");					break;										default:						$view = $controller->getView("itemlist", "html");					break;				}				$view->addTemplatePath($pluginPath.DS.'templates');				$controller->addModelPath($pluginPath.DS."models");			}										$multiimagesLang = JFactory::getLanguage();						$multiimagesLang->load("plg_system_k2multiimages", JPATH_ADMINISTRATOR);										$document = JFactory::getDocument();						$document->addScript($pluginPathSite.'/assets/highslide/highslide-full.js');						$document->addStyleSheet($pluginPathSite.'/assets/highslide/highslide.css');												$document->addScriptDeclaration("													if (typeof jQuery == 'undefined') {								document.write('<scr'+'ipt type=\"text/javascript\" src=\"".$pluginPathSite."/assets/jquery-1.7.1.min.js\"></scr'+'ipt>');								document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');							}													hs.graphicsDir = '".$pluginPathSite."/assets/highslide/graphics/';							hs.captionEval = 'this.a.rel';							hs.allowSizeReduction = true;							hs.align = 'center';							hs.zIndexCounter = '10001';							var galleryOptions = {								slideshowGroup: 'gallery',								wrapperClassName: 'dark',								dimmingOpacity: 0.7,								align: 'center',								transitions: ['expand', 'crossfade'],								fadeInOut: true,								wrapperClassName: 'borderless floating-caption',								marginLeft: 100,								marginBottom: 80,								numberPosition: 'caption'							};														// Add the slideshow controller							hs.addSlideshow({								slideshowGroup: 'gallery',								interval: 5000,								repeat: false,								useControls: true,								fixedControls: 'fit',								overlayOptions: {									className: 'text-controls',									opacity: 0.75,									position: 'bottom center',									offsetX: 0,									offsetY: -15,									hideOnMouseOut: true								}							});														//language							hs.lang = {																loadingText : '".JText::_('PLG_K2_MULTIIMAGES_LOADING')."',								nextText : '".JText::_('PLG_K2_MULTIIMAGES_NEXT')."', 								nextTitle : '".JText::_('PLG_K2_MULTIIMAGES_NEXT_TITLE')."',								closeText : '".JText::_('PLG_K2_MULTIIMAGES_CLOSE')."', 								closeTitle : '".JText::_('PLG_K2_MULTIIMAGES_CLOSE_TITLE')."', 								playText : '".JText::_('PLG_K2_MULTIIMAGES_PLAY')."',								playTitle : '".JText::_('PLG_K2_MULTIIMAGES_PLAY_TITLE')."',								pauseText : '".JText::_('PLG_K2_MULTIIMAGES_PAUSE')."',								pauseTitle : '".JText::_('PLG_K2_MULTIIMAGES_PAUSE_TITLE')."',								previousText : '".JText::_('PLG_K2_MULTIIMAGES_PREVIOUS')."',								previousTitle : '".JText::_('PLG_K2_MULTIIMAGES_PREVIOUS_TITLE')."',								fullExpandText : '".JText::_('PLG_K2_MULTIIMAGES_EXPAND')."',								fullExpandTitle : '".JText::_('PLG_K2_MULTIIMAGES_EXPAND_TITLE')."',								number: '".JText::_('PLG_K2_MULTIIMAGES_IMAGE')." %1 ".JText::_('PLG_K2_MULTIIMAGES_IMAGE_OF')." %2',								restoreTitle : '".JText::_('PLG_K2_MULTIIMAGES_RESTORE_TITLE')."'							}														");														require_once($pluginPath.DS.'models'.DS.'itemmultiimages.php');						}				//remove item in backend		if (JRequest::getVar("option") == "com_k2" && JRequest::getVar("view") == "items" && $mainframe->isAdmin() && JRequest::getVar("task") == "remove") { 			if (!defined('JPATH_ROOT')) {			   define('JPATH_ROOT', JPath::clean(JPATH_SITE));			}									if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);			if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');			if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');			if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');			require_once ($pluginPath.DS."administrator".DS."models".DS."itemsmultiimages.php");			$model = new K2ModelItemsMultiimages;							$model->remove();							}				//copy item in backend		if (JRequest::getVar("option") == "com_k2" && JRequest::getVar("view") == "items" && $mainframe->isAdmin() && JRequest::getVar("task") == "copy") { 									if (!defined('JPATH_ROOT')) {			   define('JPATH_ROOT', JPath::clean(JPATH_SITE));			}									if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);			if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_k2');			if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');			if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');			require_once ($pluginPath.DS."administrator".DS."models".DS."itemsmultiimages.php");			$model = new K2ModelItemsMultiimages;							$model->copy();							}			}		function getCatid($itemid) {		$db = JFactory::getDBO();		$query = "SELECT catid FROM #__k2_items WHERE id = {$itemid}";		$db->setQuery($query);		return $db->loadResult();	}	}
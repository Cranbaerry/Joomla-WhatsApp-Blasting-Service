<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_configs
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsconfigs\Component\Dt_whatsapp_tenants_configs\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class Dt_whatsapp_tenants_configsRouter
 *
 */
class Router extends RouterView
{
	private $noIDs;
	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 *
	 * @since  1.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	private $categoryCache = [];

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$params = ComponentHelper::getParams('com_dt_whatsapp_tenants_configs');
		$this->noIDs = (bool) $params->get('sef_ids');
		$this->categoryFactory = $categoryFactory;
		
		
			$whatsapptenantsconfigform = new RouterViewConfiguration('whatsapptenantsconfigform');
			$whatsapptenantsconfigform->setKey('id');
			$this->registerView($whatsapptenantsconfigform);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}


	
		/**
		 * Method to get the segment(s) for an whatsapptenantsconfig
		 *
		 * @param   string  $id     ID of the whatsapptenantsconfig to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantsconfigSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantsconfigform
			 *
			 * @param   string  $id     ID of the whatsapptenantsconfigform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWhatsapptenantsconfigformSegment($id, $query)
			{
				return $this->getWhatsapptenantsconfigSegment($id, $query);
			}

	
		/**
		 * Method to get the segment(s) for an whatsapptenantsconfig
		 *
		 * @param   string  $segment  Segment of the whatsapptenantsconfig to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantsconfigId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantsconfigform
			 *
			 * @param   string  $segment  Segment of the whatsapptenantsconfigform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWhatsapptenantsconfigformId($segment, $query)
			{
				return $this->getWhatsapptenantsconfigId($segment, $query);
			}

	/**
	 * Method to get categories from cache
	 *
	 * @param   array  $options   The options for retrieving categories
	 *
	 * @return  CategoryInterface  The object containing categories
	 *
	 * @since   1.0.0
	 */
	private function getCategories(array $options = []): CategoryInterface
	{
		$key = serialize($options);

		if (!isset($this->categoryCache[$key]))
		{
			$this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
		}

		return $this->categoryCache[$key];
	}
}

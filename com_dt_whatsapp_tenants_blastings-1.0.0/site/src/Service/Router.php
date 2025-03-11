<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Service;

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
 * Class Dt_whatsapp_tenants_blastingsRouter
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
		$params = ComponentHelper::getParams('com_dt_whatsapp_tenants_blastings');
		$this->noIDs = (bool) $params->get('sef_ids');
		$this->categoryFactory = $categoryFactory;
		
		
			$whatsapptenantsblastings = new RouterViewConfiguration('whatsapptenantsblastings');
			$this->registerView($whatsapptenantsblastings);
			$ccWhatsapptenantsblasting = new RouterViewConfiguration('whatsapptenantsblasting');
			$ccWhatsapptenantsblasting->setKey('id')->setParent($whatsapptenantsblastings);
			$this->registerView($ccWhatsapptenantsblasting);
			$whatsapptenantsblastingform = new RouterViewConfiguration('whatsapptenantsblastingform');
			$whatsapptenantsblastingform->setKey('id');
			$this->registerView($whatsapptenantsblastingform);
			$whatsapptenantscontacts = new RouterViewConfiguration('whatsapptenantscontacts');
			$this->registerView($whatsapptenantscontacts);
			$ccWhatsapptenantscontact = new RouterViewConfiguration('whatsapptenantscontact');
			$ccWhatsapptenantscontact->setKey('id')->setParent($whatsapptenantscontacts);
			$this->registerView($ccWhatsapptenantscontact);
			$whatsapptenantscontactform = new RouterViewConfiguration('whatsapptenantscontactform');
			$whatsapptenantscontactform->setKey('id');
			$this->registerView($whatsapptenantscontactform);
			$whatsapptenantsscheduledmessages = new RouterViewConfiguration('whatsapptenantsscheduledmessages');
			$this->registerView($whatsapptenantsscheduledmessages);
			$ccWhatsapptenantsscheduledmessage = new RouterViewConfiguration('whatsapptenantsscheduledmessage');
			$ccWhatsapptenantsscheduledmessage->setKey('id')->setParent($whatsapptenantsscheduledmessages);
			$this->registerView($ccWhatsapptenantsscheduledmessage);
			$whatsapptenantsmessagehistories = new RouterViewConfiguration('whatsapptenantsmessagehistories');
			$this->registerView($whatsapptenantsmessagehistories);
			$ccWhatsapptenantsmessagehistory = new RouterViewConfiguration('whatsapptenantsmessagehistory');
			$ccWhatsapptenantsmessagehistory->setKey('id')->setParent($whatsapptenantsmessagehistories);
			$this->registerView($ccWhatsapptenantsmessagehistory);
			$whatsapptenantskeywords = new RouterViewConfiguration('whatsapptenantskeywords');
			$this->registerView($whatsapptenantskeywords);
			$ccWhatsapptenantskeyword = new RouterViewConfiguration('whatsapptenantskeyword');
			$ccWhatsapptenantskeyword->setKey('id')->setParent($whatsapptenantskeywords);
			$this->registerView($ccWhatsapptenantskeyword);
			$whatsapptenantskeywordform = new RouterViewConfiguration('whatsapptenantskeywordform');
			$whatsapptenantskeywordform->setKey('id');
			$this->registerView($whatsapptenantskeywordform);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}


	
		/**
		 * Method to get the segment(s) for an whatsapptenantsblasting
		 *
		 * @param   string  $id     ID of the whatsapptenantsblasting to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantsblastingSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantsblastingform
			 *
			 * @param   string  $id     ID of the whatsapptenantsblastingform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWhatsapptenantsblastingformSegment($id, $query)
			{
				return $this->getWhatsapptenantsblastingSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an whatsapptenantscontact
		 *
		 * @param   string  $id     ID of the whatsapptenantscontact to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantscontactSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantscontactform
			 *
			 * @param   string  $id     ID of the whatsapptenantscontactform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWhatsapptenantscontactformSegment($id, $query)
			{
				return $this->getWhatsapptenantscontactSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an whatsapptenantsscheduledmessage
		 *
		 * @param   string  $id     ID of the whatsapptenantsscheduledmessage to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantsscheduledmessageSegment($id, $query)
		{
			return array((int) $id => $id);
		}
		/**
		 * Method to get the segment(s) for an whatsapptenantsmessagehistory
		 *
		 * @param   string  $id     ID of the whatsapptenantsmessagehistory to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantsmessagehistorySegment($id, $query)
		{
			return array((int) $id => $id);
		}
		/**
		 * Method to get the segment(s) for an whatsapptenantskeyword
		 *
		 * @param   string  $id     ID of the whatsapptenantskeyword to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWhatsapptenantskeywordSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantskeywordform
			 *
			 * @param   string  $id     ID of the whatsapptenantskeywordform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWhatsapptenantskeywordformSegment($id, $query)
			{
				return $this->getWhatsapptenantskeywordSegment($id, $query);
			}

	
		/**
		 * Method to get the segment(s) for an whatsapptenantsblasting
		 *
		 * @param   string  $segment  Segment of the whatsapptenantsblasting to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantsblastingId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantsblastingform
			 *
			 * @param   string  $segment  Segment of the whatsapptenantsblastingform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWhatsapptenantsblastingformId($segment, $query)
			{
				return $this->getWhatsapptenantsblastingId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an whatsapptenantscontact
		 *
		 * @param   string  $segment  Segment of the whatsapptenantscontact to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantscontactId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantscontactform
			 *
			 * @param   string  $segment  Segment of the whatsapptenantscontactform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWhatsapptenantscontactformId($segment, $query)
			{
				return $this->getWhatsapptenantscontactId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an whatsapptenantsscheduledmessage
		 *
		 * @param   string  $segment  Segment of the whatsapptenantsscheduledmessage to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantsscheduledmessageId($segment, $query)
		{
			return (int) $segment;
		}
		/**
		 * Method to get the segment(s) for an whatsapptenantsmessagehistory
		 *
		 * @param   string  $segment  Segment of the whatsapptenantsmessagehistory to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantsmessagehistoryId($segment, $query)
		{
			return (int) $segment;
		}
		/**
		 * Method to get the segment(s) for an whatsapptenantskeyword
		 *
		 * @param   string  $segment  Segment of the whatsapptenantskeyword to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWhatsapptenantskeywordId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an whatsapptenantskeywordform
			 *
			 * @param   string  $segment  Segment of the whatsapptenantskeywordform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWhatsapptenantskeywordformId($segment, $query)
			{
				return $this->getWhatsapptenantskeywordId($segment, $query);
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

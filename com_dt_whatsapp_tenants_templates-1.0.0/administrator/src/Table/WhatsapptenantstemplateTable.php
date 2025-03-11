<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_templates
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantstemplates\Component\Dt_whatsapp_tenants_templates\Administrator\Table;
// No direct access
defined('_JEXEC') or die;

use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Access\Access;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table as Table;
use \Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use \Joomla\Database\DatabaseDriver;
use \Joomla\CMS\Filter\OutputFilter;
use \Joomla\CMS\Filesystem\File;
use \Joomla\Registry\Registry;
use \Comdtwhatsapptenantstemplates\Component\Dt_whatsapp_tenants_templates\Administrator\Helper\Dt_whatsapp_tenants_templatesHelper;
use \Joomla\CMS\Helper\ContentHelper;


/**
 * Whatsapptenantstemplate table
 *
 * @since 1.0.0
 */
class WhatsapptenantstemplateTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
	use TaggableTableTrait;

	/**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  4.0.0
     */
    protected $_supportNullValue = true;

	
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_dt_whatsapp_tenants_templates.whatsapptenantstemplate';
		parent::__construct('#__dt_whatsapp_tenants_templates', 'id', $db);
		$this->setColumnAlias('published', 'state');
		
	}

	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   1.0.0
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     Table:bind
	 * @since   1.0.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind($array, $ignore = '')
	{
		$date = Factory::getDate();
		$task = Factory::getApplication()->input->get('task');
		$user = Factory::getApplication()->getIdentity();
		
		$input = Factory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = Factory::getUser()->id;
		}

		if ($array['id'] == 0 && empty($array['modified_by']))
		{
			$array['modified_by'] = Factory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified_by'] = Factory::getUser()->id;
		}

		// Support for multiple field: language
		if (isset($array['language']))
		{
			if (is_array($array['language']))
			{
				$array['language'] = implode(',',$array['language']);
			}
			elseif (strpos($array['language'], ',') != false)
			{
				$array['language'] = explode(',',$array['language']);
			}
			elseif (strlen($array['language']) == 0)
			{
				$array['language'] = '';
			}
		}
		else
		{
			$array['language'] = '';
		}

		// Support for multiple field: category
		if (isset($array['category']))
		{
			if (is_array($array['category']))
			{
				$array['category'] = implode(',',$array['category']);
			}
			elseif (strpos($array['category'], ',') != false)
			{
				$array['category'] = explode(',',$array['category']);
			}
			elseif (strlen($array['category']) == 0)
			{
				$array['category'] = '';
			}
		}
		else
		{
			$array['category'] = '';
		}

		// Support for multiple field: header_type
		if (isset($array['header_type']))
		{
			if (is_array($array['header_type']))
			{
				$array['header_type'] = implode(',',$array['header_type']);
			}
			elseif (strpos($array['header_type'], ',') != false)
			{
				$array['header_type'] = explode(',',$array['header_type']);
			}
			elseif (strlen($array['header_type']) == 0)
			{
				$array['header_type'] = '';
			}
		}
		else
		{
			$array['header_type'] = '';
		}
		// Support for multi file field: header_media
		if (!empty($array['header_media']))
		{
			if (is_array($array['header_media']))
			{
				$array['header_media'] = implode(',', $array['header_media']);
			}
			elseif (strpos($array['header_media'], ',') != false)
			{
				$array['header_media'] = explode(',', $array['header_media']);
			}
		}
		else
		{
			$array['header_media'] = '';
		}


		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new Registry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!$user->authorise('core.admin', 'com_dt_whatsapp_tenants_templates.whatsapptenantstemplate.' . $array['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_dt_whatsapp_tenants_templates/access.xml',
				"/access/section[@name='whatsapptenantstemplate']/"
			);
			$default_actions = Access::getAssetRules('com_dt_whatsapp_tenants_templates.whatsapptenantstemplate.' . $array['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
				if (key_exists($action->name, $default_actions))
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
				}
			}

			$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0.0
	 */
	public function store($updateNulls = true)
	{
		
		
		return parent::store($updateNulls);
	}

	/**
	 * This function convert an array of Access objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of Access objects.
	 *
	 * @return  array
	 */
	private function JAccessRulestoArray($jaccessrules)
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}
		
		
		// Support multi file field: header_media
		$app = Factory::getApplication();
		$files = $app->input->files->get('jform', array(), 'raw');
		$array = $app->input->get('jform', array(), 'ARRAY');
		if (empty($files['header_media'][0])){
			$temp = $files;
			$files = array();
			$files['header_media'][] = $temp['header_media'];
		}

		if ($files['header_media'][0]['size'] > 0)
		{
			// Deleting existing files
			$oldFiles = Dt_whatsapp_tenants_templatesHelper::getFiles($this->id, $this->_tbl, 'header_media');

			foreach ($oldFiles as $f)
			{
				$oldFile = JPATH_ROOT . '/uploads/$this->created_by/' . $f;

				if (file_exists($oldFile) && !is_dir($oldFile))
				{
					unlink($oldFile);
				}
			}

			$this->header_media = "";

			foreach ($files['header_media'] as $singleFile )
			{
				jimport('joomla.filesystem.file');

				// Check if the server found any error.
				$fileError = $singleFile['error'];
				$message = '';

				if ($fileError > 0 && $fileError != 4)
				{
					switch ($fileError)
					{
						case 1:
							$message = Text::_('File size exceeds allowed by the server');
							break;
						case 2:
							$message = Text::_('File size exceeds allowed by the html form');
							break;
						case 3:
							$message = Text::_('Partial upload error');
							break;
					}

					if ($message != '')
					{
						$app->enqueueMessage($message, 'warning');

						return false;
					}
				}
				elseif ($fileError == 4)
				{
					if (isset($array['header_media']))
					{
						$this->header_media = $array['header_media'];
					}
				}
				else
				{

					// Replace any special characters in the filename
					jimport('joomla.filesystem.file');
					$filename = File::stripExt($singleFile['name']);
					$extension = File::getExt($singleFile['name']);
					$filename = preg_replace("/[^A-Za-z0-9]/i", "-", $filename);
					$filename = $filename . '.' . $extension;
					$uploadPath = JPATH_ROOT . '/uploads'.'/' . Factory::getApplication()->getIdentity()->id. '/' .$filename;
					$fileTemp = $singleFile['tmp_name'];

					if (!File::exists($uploadPath))
					{
						if (!File::upload($fileTemp, $uploadPath))
						{
							$app->enqueueMessage('Error moving file', 'warning');

							return false;
						}
					}

					$this->header_media .= (!empty($this->header_media)) ? "," : "";
					$this->header_media .= $filename;
				}
			}
		}
		else
		{
			$this->header_media .= $array['header_media_hidden'];
		}

		return parent::check();
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @see Table::_getAssetName
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return $this->typeAlias . '.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   Table   $table  Table name
	 * @param   integer  $id     Id
	 *
	 * @see Table::_getAssetParentId
	 *
	 * @return mixed The id on success, false on failure.
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_dt_whatsapp_tenants_templates');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	//XXX_CUSTOM_TABLE_FUNCTION

	
    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return bool
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        
		if ($result)
		{
			jimport('joomla.filesystem.file');

			$checkImageVariableType = gettype($this->header_media);

			switch ($checkImageVariableType)
			{
			case 'string':
				File::delete(JPATH_ROOT . '/uploads/'. $this->created_by . '/'  . $this->header_media);
			break;
			default:
			foreach ($this->header_media as $header_mediaFile)
			{
				File::delete(JPATH_ROOT . '/uploads/'.$this->created_by .'/'  . $header_mediaFile);
			}
			}
		}

        return $result;
    }
}

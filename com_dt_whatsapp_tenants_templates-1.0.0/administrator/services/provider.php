<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_templates
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Comdtwhatsapptenantstemplates\Component\Dt_whatsapp_tenants_templates\Administrator\Extension\Dt_whatsapp_tenants_templatesComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;


/**
 * The Dt_whatsapp_tenants_templates service provider.
 *
 * @since  1.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function register(Container $container)
	{

		$container->registerServiceProvider(new CategoryFactory('\\Comdtwhatsapptenantstemplates\\Component\\Dt_whatsapp_tenants_templates'));
		$container->registerServiceProvider(new MVCFactory('\\Comdtwhatsapptenantstemplates\\Component\\Dt_whatsapp_tenants_templates'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Comdtwhatsapptenantstemplates\\Component\\Dt_whatsapp_tenants_templates'));
		$container->registerServiceProvider(new RouterFactory('\\Comdtwhatsapptenantstemplates\\Component\\Dt_whatsapp_tenants_templates'));

		$container->set(
			ComponentInterface::class,
			function (Container $container)
			{
				$component = new Dt_whatsapp_tenants_templatesComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};

<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_webhook
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
use Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Administrator\Extension\Dt_whatsapp_tenants_webhookComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;


/**
 * The Dt_whatsapp_tenants_webhook service provider.
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

		$container->registerServiceProvider(new CategoryFactory('\\Comdtwhatsapptenantswebhook\\Component\\Dt_whatsapp_tenants_webhook'));
		$container->registerServiceProvider(new MVCFactory('\\Comdtwhatsapptenantswebhook\\Component\\Dt_whatsapp_tenants_webhook'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Comdtwhatsapptenantswebhook\\Component\\Dt_whatsapp_tenants_webhook'));
		$container->registerServiceProvider(new RouterFactory('\\Comdtwhatsapptenantswebhook\\Component\\Dt_whatsapp_tenants_webhook'));

		$container->set(
			ComponentInterface::class,
			function (Container $container)
			{
				$component = new Dt_whatsapp_tenants_webhookComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};

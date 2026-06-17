<?php
namespace Omeka\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use Omeka\Controller\Admin\AssetController;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AssetControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, ?array $options = null)
    {
        return new AssetController(
            $services->get('Omeka\EntityManager')
        );
    }
}

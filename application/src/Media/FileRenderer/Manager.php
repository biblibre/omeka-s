<?php
namespace Omeka\Media\FileRenderer;

use Omeka\Media\FileRenderer\RendererInterface;
use Zend\ServiceManager\AbstractPluginManager;

class Manager extends AbstractPluginManager
{
    protected $instanceOf = RendererInterface::class;
}

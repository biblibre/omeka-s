<?php
namespace Omeka\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * View helper for getting resources Id, type and resource_type as an array
 */
class RelatedRessourcesToAsset extends AbstractHelper
{
    /**
     * @param array $resource
     * @return array
     */
    public function __invoke($resource = null)
    {
        return $this->getview()->partial(
            'common/related-resources-to-asset',
            ['resources' => $resource,
                'view' => $this->getView()]
        );

    }
}

<?php
namespace Omeka\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * View helper for getting resources Id, type and resource_type as an array
 */
class RelatedResourcesToAsset extends AbstractHelper
{
    /**
     * @param array $resources
     * @return array
     */
    public function __invoke($resources = null)
    {
        return $this->getView()->partial(
            'common/related-resources-to-asset',
            [
                'resources' => $resources,
            ]
        );
    }
}

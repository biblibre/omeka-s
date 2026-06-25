<?php
namespace Omeka\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * View helper for listing resources  type,id and name from a resource array
 */
class ListResources extends AbstractHelper
{
    /**
     * Lists resources by type,id and name.
     *
     * @param .. $mig
     */
    // @return string

    public function __invoke($mig = null)
    {
        $view = $this->getView();
        $hyperlink = $view->plugin('hyperlink');

        $output = '(' . $mig->getResourceName() . ')';
        switch ($mig->getResourceName()) {
            case 'items':
                $output .= $hyperlink($mig->getTitle() . ' :' . $mig->getId(), $this->getView()->url('admin/default', ['controller' => 'item', 'action' => 'browse'], ['query' => ['id' => $mig->getId()]]));
                break;

            case 'item_sets':
                $output .= $hyperlink($mig->getTitle() . ' :' . $mig->getId(), $this->getView()->url('admin/default', ['controller' => 'item-set', 'action' => 'browse'], ['query' => ['id' => $mig->getId()]]));
                break;

            case 'media':
                $output .= $hyperlink($mig->getTitle() . ' :' . $mig->getId(), $this->getView()->url('admin/default', ['controller' => 'media', 'action' => 'browse'], ['query' => ['id' => $mig->getId()]]));
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid resource type "%s"', $mig->getResourceName()));
        }

        return $output;

    }
}

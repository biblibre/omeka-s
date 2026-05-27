<?php
namespace Omeka\Form;

use Omeka\Form\Element\ResourceSelect;

use Laminas\Form\Form;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\Event;

class AssetForm extends Form implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;


    public function init()
    {
    
        $this->add([
            'name' => 'fulltext_search',
            'type' => 'Text',
            'options' => [
                'label' => 'Search by name', // @translate
            ],
            'attributes' => [
                'id' => 'fulltext_search',
            ],
        ]);



        $this->add([
            'name' => 'owner_id',
            'type' => ResourceSelect::class, 
            'options' => [
                'label' => 'Search by owner', // @translate
                'empty_option' => 'Select user...',// @translate
                'resource_value_options' => [
                    'resource' => 'users', 
                    'option_text_callback' => 
                        function ($user) {
                            return $user->name() . ' (' . $user->email() . ')';
                        },
                ],
            ],
            'attributes' => [
                'id' => 'owner_id',
            ],
        ]);

        $addEvent = new Event('form.add_elements', $this);
        $this->getEventManager()->triggerEvent($addEvent);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add(['name' => 'fulltext_search', 'required' => false]);
        $inputFilter->add(['name' => 'owner_id', 'required' => false]);

        $this->getEventManager()->triggerEvent(new Event('form.add_elements', $this));
    }
}

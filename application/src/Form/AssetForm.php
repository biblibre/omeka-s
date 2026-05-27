<?php
namespace Omeka\Form;

use Omeka\Form\Element\ResourceSelect;

use Laminas\Form\Form;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\Event;

// use Omeka\Entity\FulltextSearch;
// use Laminas\Filter\StringTrim;
// use Laminas\Form\Annotation;
// use Laminas\Validator\Regex;
                // #[Annotation\Filter(StringTrim::class)]
                // #[Annotation\Validator(StringLength::class, options: ["min" => 1, "max" => 25])]
                // #[Annotation\Validator(Regex::class, options: ["pattern" => "/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"])]

// for the @ : input and/or input filter

class AssetForm extends Form implements EventManagerAwareInterface
{
    use EventManagerAwareTrait; //this has an interface for what ?


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
            'type' => ResourceSelect::class, //'select' but dynamic with resource select
            'options' => [
                'label' => 'Search by owner', // @translate
                // 'info' => 'Searches for assets that are owned by this user. ^ ^ ', // @translate
                'empty_option' => 'Select user...',
                'resource_value_options' => [
                    'resource' => 'users', 
                    // 'query' => [],
                    'option_text_callback' => function ($user) {
                        return $user->name() . ' (' . $user->email() . ')';
                        // add the link 
                        // $ownerText = $this->hyperlink(
                        // $user->name(),
                        // $this->url('admin/id', [
                        // 'controller' => 'user',
                        // 'action' => 'show',
                        // 'id' => $user->id()]
                        //     )
                        // );
                        // return $ownerText;
                    },
                ],
            ],
            'attributes' => [
                'id' => 'owner_id',
            ],
        ]);

        $addEvent = new Event('form.add_elements', $this);
        $this->getEventManager()->triggerEvent($addEvent);

        // $inputFilter = $this->getInputFilter();
        //  $inputFilter->add([
        //     'name' => 'o:owner[o:id]',
        //     'required' => false,
        // ]);

        // $filterEvent = new Event('form.add_input_filters', $this, ['inputFilter' => $inputFilter]);
        // $this->getEventManager()->triggerEvent($filterEvent);


        $inputFilter = $this->getInputFilter();
        $inputFilter->add(['name' => 'fulltext_search', 'required' => false]);
        $inputFilter->add(['name' => 'owner_id', 'required' => false]);

         // Optionnel : Déclencher l'événement pour permettre à d'autres modules d'ajouter des champs
        $this->getEventManager()->triggerEvent(new Event('form.add_elements', $this));
   
    }
}

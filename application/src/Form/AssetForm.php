<?php
namespace Omeka\Form;

// use Omeka\View\Helper\UserSelect; // c est celui la qui fait le "empty option" mais il fait pas la liste des users (pas de fonction)

use Omeka\Form\Element\UserSelect; // fontion qui fiat l enumeration des users mais pas d 'empty option;
// use Omeka\Form\Element\ResourceSelect;

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
            'name' => 'fulltext_search', // -> renommer en 'search'
            'type' => 'Text',
            'options' => [
                'label' => 'Search by name', // @translate
            ],
            'attributes' => [
                'id' => 'fulltext_search',
                // 'required' => false,
            ],
        ]);

        $this->add([
            'name' => 'owner_id',
            'type' => UserSelect::class, // ResourceSelect::class,   --> how to call user select if it is in view helper ?(je ne suis pas sur comment le faire )

            // voir quoi laisser
            'options' => [
                'label' => 'Search by owner', // @translate
                'info' => 'Searches for assets that are owned by this user.', // @translate
                'empty_option' => 'Select user...', // @translate
            ],
            // quoi servent les attt sinon quoi mettre et quoi enlever
            'attributes' => [
                'id' => 'owner_id',
                // 'required' => false,
            ],
        ]);

        $addEvent = new Event('form.add_elements', $this);
        $this->getEventManager()->triggerEvent($addEvent);

        // meme si je commente cette parite et je de-commente le 'required' => false, je recoit le ùessage de CSRF !;
        // doit avoir input filter pour CSRF ?
        $inputFilter = $this->getInputFilter();
        $inputFilter->add(['name' => 'fulltext_search', 'required' => true]);
        $inputFilter->add(['name' => 'owner_id', 'required' => true]);

        $this->getEventManager()->triggerEvent(new Event('form.add_elements', $this));
    }
}

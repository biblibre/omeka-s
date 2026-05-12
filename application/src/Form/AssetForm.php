<?php
namespace Omeka\Form;

use Omeka\Form\Element\ResourceSelect;

use Omeka\Entity\FulltextSearch;

use Laminas\Form\Form;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\Event;

class AssetForm extends Form implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var array
     */
   

    /*
    les 2 parties a mettre sont ceux du forme alors : le fulltextsearch et le form de choix multiple de owner 

    alors avoir 
    - this add name 
    -this add owner

    rq : voir quoi faire pour le fulltextsearch : est ce que c'est pour faire une recherche dans les assets ? ou pour faire une recherche dans les users pour le choix du owner ?
     
    - voir quoi faire pour  le 'EVENT'
    */

    protected $fulltextSearch;
   
    public function init()
    {
        $asset = $this->getAsset();  // pas sur ca fonctionne, a revoir autre paussibilite est de la remplacer par ce code : $asset = $this->getOption('asset'); et de lui passer l'asset en option dans le factory
        //$urlHelper = $this->getUrlHelper();

        $this->add([
            'name' => 'o:name',
            'type' => 'Text',
            'options' => [
                'label' => 'Search By Name', // @translate
            ],
            'attributes' => [
                'id' => 'asset-name',
                //'required' => false,
            ],
        ]);

        //call for the dropsow : owner;
        $owner = $asset? $asset->owner() : null;
        $this->add([
            'name' => 'o:owner[o:id]',
            'type' => ResourceSelect::class,
            'attributes' => [
                'id' => 'resource-owner-select',
                'class' => 'chosen-select',
                // 'data-api-base-url' => $urlHelper('api-local/default', ['resource' => 'users']),
            ],
            'options' => [
                'label' => 'Owner', // @translate
                'empty_option' => $owner ? null : '[No owner]', // @translate
                'resource_value_options' => [
                    'resource' => 'users',
                    'query' => [],
                    'option_text_callback' => function ($user) {
                        return $user->name();
                    },
                ],
            ],
        ]);


        $addEvent = new Event('form.add_elements', $this);
        $this->getEventManager()->triggerEvent($addEvent);

        $inputFilter = $this->getInputFilter();
         $inputFilter->add([
            'name' => 'o:owner[o:id]',
            'required' => false,
        ]);

        $filterEvent = new Event('form.add_input_filters', $this, ['inputFilter' => $inputFilter]);
        $this->getEventManager()->triggerEvent($filterEvent);
    }


    /**
     * @param fulltextSearch $fulltextSearch
     */
    public function setFulltextSearch($fulltextSearch)
    {
        $this->fulltextSearch = $fulltextSearch;
    }

    /**
     * @return fulltextSearch
     */
    public function getFulltextSearch()
    {
        return $this->fulltextSearch;
    }

}

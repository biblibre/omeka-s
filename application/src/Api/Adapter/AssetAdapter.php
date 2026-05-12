<?php
namespace Omeka\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\File\Validator;
use Omeka\Stdlib\ErrorStore;

class AssetAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'media_type' => 'mediaType',
        'name' => 'name',
        'extension' => 'extension',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'media_type' => 'mediaType',
        'name' => 'name',
        'extension' => 'extension',
        'storage_id' => 'storageId',
        'alt_text' => 'altText',
        'owner' => 'owner',
    ];

    public function getResourceName()
    {
        return 'assets';
    }

    public function getRepresentationClass()
    {
        return \Omeka\Api\Representation\AssetRepresentation::class;
    }

    public function getEntityClass()
    {
        return \Omeka\Entity\Asset::class;
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    { if (isset($query['fulltext_search']) && trim($query['fulltext_search']) !== '') {
            $searchTerm = trim($query['fulltext_search']);
            
            $param = $qb->createNamedParameter('%' . $searchTerm . '%'); // " Paris "

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('omeka_root.name', $param),   
                )
            );
        }

       

       
/*

 if (isset($query['fulltext_search']) && trim($query['fulltext_search']) !== '') {
            $searchTerm = trim($query['fulltext_search']);
            $param = $qb->createNamedParameter($searchTerm);
            
            // Create the 4 standard "Whole Word" patterns
            $start  = $qb->createNamedParameter($searchTerm . ' %'); // "Paris "
            $middle = $qb->createNamedParameter('% ' . $searchTerm . ' %'); // " Paris "
            $end    = $qb->createNamedParameter('% ' . $searchTerm); // " Paris"



            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('omeka_root.name', $param),    // Exact match
                    $qb->expr()->like('omeka_root.name', $start),  // Starts with word
                    $qb->expr()->like('omeka_root.name', $middle), // Middle of sentence
                    $qb->expr()->like('omeka_root.name', $end)     // Ends with word
                )
            );
        }





  if (isset($query['search'])) { 
            $searchTerm = trim($query['fulltext_search']);
            $param = $qb->createNamedParameter($searchTerm);
            $qb->andWhere(
                $qb->expr()->eq('omeka_root.name', $param)
            );
        }

     

            $qb->expr()->foreach->eq(trim(tolower('omeka_root.name', $expr)))
            
  

  asset->name 
        if (isset($query['fulltext_search'])) {

           $searchTerm = $query['fulltext_search'];
            $expr = $qb->createNamedParameter($searchTerm);

            $qb->andWhere(
                $qb->expr()->eq('omeka_root.name', $expr)
            );
     

            'omeka_root.name', // 'omeka_root' is the alias for the Asset table


    if (isset($query['search'])) {
    $searchTerm = '%' . $query['search'] . '%';
    $param = $qb->createNamedParameter($searchTerm);

    // This is the "Full-text" equivalent for Assets
    $qb->andWhere(
        $qb->expr()->orX(
            $qb->expr()->like('omeka_root.name', $param),
            $qb->expr()->like('omeka_root.extension', $param),
            $qb->expr()->like('omeka_root.mediaType', $param)
        )
    );
}




        if (!empty($query['name'])) {
            $qb->andWhere($qb->expr()->eq(
                "omeka_root.name",
                $qb->createNamedParameter($query['name']))
            );
        }

        parent::buildQuery($qb, $query); marche pas car le parent ne contient pas buildquery; 
        voir quoi faire !
        
        if (isset($query['search'])) {
            $this->buildPropertyQuery($qb, ['property' => [[
                'property' => null,
                'type' => 'in',
                'text' => $query['search'],
            ]]]);
        }
*/

        
        if (isset($query['owner_id']) && is_numeric($query['owner_id'])) {
            $userAlias = $qb->createAlias();
            if (0 == $query['owner_id']) {
                // Search assets without an owner.
                $qb->andWhere($qb->expr()->isNull('omeka_root.owner'));
            } else {
                $qb->innerJoin(
                    'omeka_root.owner',
                    $userAlias
                );
                $qb->andWhere($qb->expr()->eq(
                    "$userAlias.id",
                    $this->createNamedParameter($qb, $query['owner_id']))
                );
            }
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $data = $request->getContent();

        if (Request::CREATE === $request->getOperation()) {
            $fileData = $request->getFileData();
            if (!isset($fileData['file'])) {
                $errorStore->addError('file', 'No file was uploaded');
                return;
            }

            $uploader = $this->getServiceLocator()->get('Omeka\File\Uploader');
            $tempFile = $uploader->upload($fileData['file'], $errorStore);
            if (!$tempFile) {
                return;
            }

            $tempFile->setSourceName($fileData['file']['name']);
            $config = $this->getServiceLocator()->get('Config');
            $validator = new Validator($config['api_assets']['allowed_media_types'], $config['api_assets']['allowed_extensions']);
            if (!$validator->validate($tempFile, $errorStore)) {
                return;
            }

            $this->hydrateOwner($request, $entity);
            $entity->setStorageId($tempFile->getStorageId());
            $entity->setExtension($tempFile->getExtension());
            $entity->setMediaType($tempFile->getMediaType());
            $entity->setName($request->getValue('o:name', $fileData['file']['name']));

            $tempFile->storeAsset();
            $tempFile->delete();
        } else {
            if ($this->shouldHydrate($request, 'o:name')) {
                $entity->setName($request->getValue('o:name'));
            }
        }

        if ($this->shouldHydrate($request, 'o:alt_text')) {
            $entity->setAltText($request->getValue('o:alt_text'));
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        // Don't add this name error if we have any other errors already
        if ($errorStore->hasErrors()) {
            return;
        }

        $name = $entity->getName();
        if (!is_string($name) || $name === '') {
            $errorStore->addError('o:name', 'An asset must have a name.');
        }
    }
}

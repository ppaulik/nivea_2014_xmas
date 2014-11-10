<?php


namespace CmsModule\Doctrine\Controls;

use CmsModule\Doctrine\EntityFormMapper;
use CmsModule\Doctrine\IComponentMapper;
use CmsModule\Doctrine\ToManyContainer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nette;
use Nette\ComponentModel\Component;


class ToMany extends Nette\Object implements IComponentMapper
{

    /**
     * @var EntityFormMapper
     */
    private $mapper;


    public function __construct(EntityFormMapper $mapper)
    {
        $this->mapper = $mapper;
    }


    /**
     * {@inheritdoc}
     */
    public function load(ClassMetadata $meta, Component $component, $entity)
    {

        if (!$component instanceof ToManyContainer) {
            return FALSE;
        }

        if (!$collection = $this->getCollection($meta, $entity, $name = $component->getName())) {
            return FALSE;
        }

        $em  = $this->mapper->getEntityManager();
        $UoW = $em->getUnitOfWork();

        $component->bindCollection($entity, $collection);



        if (!empty($collection)) {

            foreach ($collection as $relation) {

                if ($id = $UoW->getSingleIdentifierValue($relation)) {
                    $this->mapper->load($relation, $component[$id]);
                    continue;
                }

                $this->mapper->load($relation, $component[ToManyContainer::NEW_PREFIX . $collection->indexOf($relation)]);
            }
            return TRUE;

        }

        return false;

    }


    /**
     * {@inheritdoc}
     */
    public function save(ClassMetadata $meta, Component $component, $entity)
    {
        if (!$component instanceof ToManyContainer) {
            return FALSE;
        }

        if (!$collection = $this->getCollection($meta, $entity, $component->getName(), true)) {
            return FALSE;
        }

        $em           = $this->mapper->getEntityManager();
        $class        = $meta->getAssociationTargetClass($component->getName());
        $relationMeta = $em->getClassMetadata($class);


        /** @var Nette\Forms\Container $container */
        foreach ($component->getComponents(FALSE, 'Nette\Forms\Container') as $container) {

            $isNew = substr($container->getName(), 0, strlen(ToManyContainer::NEW_PREFIX)) === ToManyContainer::NEW_PREFIX;

            $indexCollection = $this->getIndexCollectionOfId($relationMeta, $collection->toArray(), $container->getName());

            $name = $isNew ? substr($container->getName(), strlen(ToManyContainer::NEW_PREFIX)) : $indexCollection;
            if (!$relation = $collection->get($name)) { // entity was added from the client

                if (!$component->isAllowedRemove()) {
					continue;
                }

                $collection[$name] = $relation = new $relationMeta->name;

                // inverse associations
                foreach ($relationMeta->getAssociationMappings() as $association => $assocMapping) {
                    $entity_class = $relationMeta->getAssociationTargetClass($association);
                    if ($entity instanceof $entity_class) {
                        $relation->$association = $entity;
                    }
                }
            }

            $this->mapper->save($relation, $container);
        }

        return TRUE;
    }


    /**
     * @param ClassMetadata $meta
     * @param object        $entity
     * @param string        $field
     *
     * @return Collection
     */
    private function getCollection(ClassMetadata $meta, $entity, $field)
    {
        if (!$meta->hasAssociation($field) || $meta->isSingleValuedAssociation($field)) {
            return FALSE;
        }

        $collection = $meta->getFieldValue($entity, $field);


        if ($collection === NULL) {

            $collection = new ArrayCollection();
            $meta->setFieldValue($entity, $field, $collection);
        }

        return $collection;
    }


    /**
     * @param ClassMetadata $meta
     * @param array         $collection
     *
     * @return array
     */
    private function sortCollection(ClassMetadata $meta, array $collection)
    {
        $id     = $meta->getSingleIdentifierFieldName();
        $result = array();
        array_walk($collection, function (&$value) use (&$result, $id) {
            $result[$value->$id] = $value;
        });
        return $result;
    }


    /**
     * @param ClassMetadata $meta
     * @param               $collectionArray
     * @param               $idValue
     *
     * @return array
     */
    private function getIndexCollectionOfId(ClassMetadata $meta, $collectionArray, $idValue)
    {
        $id     = $meta->getSingleIdentifierFieldName();
        $result = null;

        foreach ($collectionArray as $key => $obj) {
            if ($obj->$id == $idValue) {
                $result = $key;
                break;
            }
        }
        return $result;

    }


}

<?php


namespace CmsModule\Doctrine\Controls;

use CmsModule\Doctrine\EntityFormMapper;
use CmsModule\Doctrine\IComponentMapper;
use CmsModule\Doctrine\InvalidStateException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\PersistentCollection;
use Nette;
use Nette\ComponentModel\Component;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\CheckboxList;
use Symfony\Component\PropertyAccess\PropertyAccessor;


/**
 */
class TextControl extends Nette\Object implements IComponentMapper
{

    /**
     * @var EntityFormMapper
     */
    private $mapper;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var EntityManager
     */
    private $em;


    public function __construct(EntityFormMapper $mapper)
    {
        $this->mapper   = $mapper;
        $this->em       = $this->mapper->getEntityManager();
        $this->accessor = $mapper->getAccessor();
    }


    /**
     * {@inheritdoc}
     */
    public function load(ClassMetadata $meta, Component $component, $entity)
    {

        if (!$component instanceof BaseControl) {
            return FALSE;
        }

        if ($meta->hasField($name = $component->getOption(self::FIELD_NAME, $component->getName()))) {
            $component->setValue($this->accessor->getValue($entity, $name));
            return TRUE;
        }

        if (!$meta->hasAssociation($name)) {
            return FALSE;
        }

        /** @var SelectBox|RadioList|MultiSelectBox|CheckboxList $component */
        if (($component instanceof SelectBox ||
             $component instanceof RadioList ||
             $component instanceof MultiSelectBox ||
             $component instanceof CheckboxList) && !count($component->getItems())
        ) {
            // items load
            if (!$nameKey = $component->getOption(self::ITEMS_TITLE, FALSE)) {
                $path = $component->lookupPath('Nette\Application\UI\Form');
                throw new InvalidStateException(
                    'Either specify items for ' . $path . ' yourself, or set the option Kdyby\DoctrineForms\IComponentMapper::ITEMS_TITLE ' .
                    'to choose field that will be used as title'
                );
            }

            $criteria = $component->getOption(self::ITEMS_FILTER, array());
            $orderBy  = $component->getOption(self::ITEMS_ORDER, array());

            $related = $this->relatedMetadata($entity, $name);
            $items   = $this->findPairs($related, $criteria, $orderBy, $nameKey);
            $component->setItems($items);
        }

        // values load
        $relationMapping = $meta->getAssociationMapping($name);
        if ($relationMapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {

            /** @var $component MultiSelectBox || CheckboxList */
            if ($component instanceof Nette\Forms\Controls\MultiSelectBox ||
                $component instanceof Nette\Forms\Controls\CheckboxList) {

                if ($relation = $this->accessor->getValue($entity, $name)) {

                    $UoW = $this->em->getUnitOfWork();

                    $values = array();
                    foreach ($relation as $value) {
                        $id = $UoW->getSingleIdentifierValue($value);
                        $values[] = $id;

                    }

                    $component->setValue($values);
                    return TRUE;
                }
            }

            return FALSE;

        } else {

            if ($relation = $this->accessor->getValue($entity, $name)) {
                $UoW = $this->em->getUnitOfWork();
                $component->setValue($UoW->getSingleIdentifierValue($relation));
            }
        }

        return TRUE;
    }


    /**
     * @param string|object $entity
     * @param string        $relationName
     *
     * @return ClassMetadata|\Kdyby\Doctrine\Mapping\ClassMetadata
     */
    private function relatedMetadata($entity, $relationName)
    {
        $meta        = $this->em->getClassMetadata(is_object($entity) ? get_class($entity) : $entity);
        $targetClass = $meta->getAssociationTargetClass($relationName);
        return $this->em->getClassMetadata($targetClass);
    }


    /**
     * @param ClassMetadata $meta
     * @param array         $criteria
     * @param array         $orderBy
     * @param string        $nameKey
     *
     * @return array
     */
    private function findPairs(ClassMetadata $meta, $criteria, $orderBy, $nameKey)
    {
        $repository = $this->em->getRepository($meta->getName());

        if ($repository instanceof \Kdyby\Doctrine\EntityDao) {
            return $repository->findPairs($criteria, $nameKey, $orderBy);
        }

        $items = array();
        $idKey = $meta->getSingleIdentifierFieldName();
        foreach ($repository->findBy($criteria, $orderBy) as $entity) {
            $items[$this->accessor->getValue($entity, $idKey)] = $this->accessor->getValue($entity, $nameKey);
        }

        return $items;
    }


    /**
     * {@inheritdoc}
     */
    public function save(ClassMetadata $meta, Component $component, $entity)
    {
        if (!$component instanceof BaseControl) {
            return FALSE;
        }

        if ($meta->hasField($name = $component->getOption(self::FIELD_NAME, $component->getName()))) {

            $value = $component->getValue() instanceof Nette\Http\FileUpload
                ? $component->getValue()->name
                : $component->getValue();

            $this->accessor->setValue($entity, $name, $value);
            return TRUE;
        }

        if (!$meta->hasAssociation($name)) {
            return FALSE;
        }

        if (!$identifier = $component->getValue()) {
            return FALSE;
        }


        $repository = $this->em->getRepository($this->relatedMetadata($entity, $name)->getName());

        if (is_array($identifier)) {

            /** @var $targetEntity PersistentCollection */
            if ((!($targetEntity = $this->accessor->getValue($entity, $name)) instanceof PersistentCollection) &&
                (!$targetEntity instanceof ArrayCollection)) {
                throw new InvalidStateException('Set getter "' . $name . '" in ' . get_class($entity) . " to ArrayCollection");
            }

            $relations = $repository->findAssoc(array());
            foreach ($relations as $id => $relation) {
                if ($targetEntity->contains($relations[$id]) && !in_array($id, $identifier)) {
                    $targetEntity->removeElement($relation);

                } elseif (!$targetEntity->contains($relations[$id]) && in_array($id, $identifier)) {
                    $targetEntity->add($relation);
                }
            }

            $meta->setFieldValue($entity, $name, $targetEntity);

        } else {
            if ($relation = $repository->find($identifier)) {
                $meta->setFieldValue($entity, $name, $relation);
            }
        }


        return TRUE;
    }

}

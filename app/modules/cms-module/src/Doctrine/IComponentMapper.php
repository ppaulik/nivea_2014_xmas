<?php


namespace CmsModule\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Nette;
use Nette\ComponentModel\Component;



interface IComponentMapper
{

	const FIELD_NAME = 'field.name';
	const ITEMS_TITLE = 'items.title';
	const ITEMS_FILTER = 'items.filter';
	const ITEMS_ORDER = 'items.order';



	/**
	 * @param ClassMetadata $meta
	 * @param Component $component
	 * @param object $entity
	 * @throws InvalidStateException
	 * @return
	 */
	function load(ClassMetadata $meta, Component $component, $entity);



	/**
	 * @param ClassMetadata $meta
	 * @param Component $component
	 * @param object $entity
	 * @throws InvalidStateException
	 * @return
	 */
	function save(ClassMetadata $meta, Component $component, $entity);

}

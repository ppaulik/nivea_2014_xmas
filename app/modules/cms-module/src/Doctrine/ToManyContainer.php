<?php


namespace CmsModule\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nette;
use Nette\Application\UI;



class ToManyContainer extends Nette\Forms\Container
{

	const NEW_PREFIX = '_new_';

	/**
	 * @var Collection
	 */
	private $collection;

	/**
	 * @var object
	 */
	private $parentEntity;

	/**
	 * @var Nette\Utils\Callback
	 */
	private $containerFactory;

	/**
	 * @var string
	 */
	private $containerClass = 'Nette\Forms\Container';

	/**
	 * @var bool
	 */
	private $allowRemove = FALSE;

	/**
	 * @var bool
	 */
	private $disableAdding = FALSE;



	public function __construct($containerFactory)
	{
		parent::__construct();

		$this->containerFactory = callback($containerFactory);
		$this->collection = new ArrayCollection();
	}



	protected function validateParent(Nette\ComponentModel\IContainer $parent)
	{
		parent::validateParent($parent);
		$this->monitor('Nette\Application\UI\Presenter');
	}



	public function bindCollection($parent, Collection $collection)
	{
		if (!is_object($parent)) {
			throw new InvalidArgumentException('Expected entity, but ' . gettype($parent) . ' given.');
		}

		$this->parentEntity = $parent;
		$this->collection = $collection;
	}



	/**
	 * @param string $containerClass
	 * @throws InvalidArgumentException
	 * @return ToManyContainer
	 */
	public function setContainerClass($containerClass)
	{
		if (!is_subclass_of($containerClass, 'Nette\Forms\Container')) {
			throw new InvalidArgumentException('Expected descendant of Nette\Forms\Container, but ' . $containerClass . ' given.');
		}

		$this->containerClass = $containerClass;
		return $this;
	}



	/**
	 * @param boolean $allowRemove
	 * @return ToManyContainer
	 */
	public function setAllowRemove($allowRemove = TRUE)
	{
		$this->allowRemove = (bool) $allowRemove;
		return $this;
	}



	/**
	 * @return boolean
	 */
	public function isAllowedRemove()
	{
		return $this->allowRemove;
	}



	/**
	 * @param boolean $disableAdding
	 * @return ToManyContainer
	 */
	public function setDisableAdding($disableAdding = TRUE)
	{
		$this->disableAdding = (bool) $disableAdding;
		return $this;
	}



	/**
	 * @return boolean
	 */
	public function isDisabledAdding()
	{
		return $this->disableAdding;
	}



	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCollection()
	{
		return $this->collection;
	}



	protected function createComponent($name)
	{
		$class = $this->containerClass;
		$this[$name] = $container = new $class();
		$this->containerFactory->invoke($container, $this->parent);

		return $container;
	}



	/**
	 * @param \Nette\ComponentModel\Container $obj
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if (!$obj instanceof UI\Presenter) {
			return;
		}

		/** @var UI\Form|EntityForm $form */
		$form = $this->getForm();

		if (!$form->isSubmitted()) {
			return;
		}

		foreach (array_keys($this->getHttpData()) as $id) {
			$this->getComponent($id); // eager initialize
		}
	}



	/**
	 * @return array
	 */
	private function getHttpData()
	{
		$path = $this->lookupPath('Nette\Application\UI\Form');
		$allData = $this->getForm()->getHttpData();
		return Nette\Utils\Arrays::get($allData, $path, NULL);
	}



	public static function register($name = 'toMany')
	{
		Nette\Forms\Container::extensionMethod($name, function (Nette\Forms\Container $_this, $name, $containerFactory = NULL) {
			$container = new ToManyContainer($containerFactory);

			return $_this[$name] = $container;
		});
	}

}

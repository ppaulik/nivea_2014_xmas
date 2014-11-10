<?php


namespace CmsModule\Doctrine\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @property int $id
 * @property string $name
 */
trait NamedEntityTrait
{

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @return int
	 */
	final public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->name;
	}

}

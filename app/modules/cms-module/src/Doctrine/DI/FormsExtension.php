<?php


namespace CmsModule\Doctrine\DI;

use Nette;
use Nette\PhpGenerator as Code;



/**
 * @author Pavel Paulik <pavel@paulik.seznam.cz>
 */
class FormsExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
        $builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('entityFormMapper'))
			->setClass('CmsModule\Doctrine\EntityFormMapper');

//		$builder->addDefinition($this->prefix('controlFactory'))
//			->setClass('Kdyby\DoctrineForms\Builder\ControlFactory');
//
//		$builder->addDefinition($this->prefix('builderFactory'))
//			->setClass('Devrun\DoctrineForms\BuilderFactory');
	}



	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('doctrineForms', new FormsExtension());
		};
	}

}


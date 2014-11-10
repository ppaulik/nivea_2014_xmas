<?php
namespace App\Presenters;

use Nette;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @persistent */
    public $locale;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \WebLoader\LoaderFactory @injectOFF */
    public $webLoader;

    /** @var \Kdyby\Translation\Translator */
    protected $translator;


    /**
     * @param \Kdyby\Translation\Translator $translator
     *
     * @return void
     */
    public function injectTranslator(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }

    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }


}

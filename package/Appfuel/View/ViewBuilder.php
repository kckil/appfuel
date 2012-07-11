<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\View;

use DomainException,
    RunTimeException,
    InvalidArgumentException,
    Appfuel\Console\ConsoleTemplate,
    Appfuel\View\CsvTemplate,
    Appfuel\View\AjaxTemplate,
    Appfuel\View\ViewTemplate,
    Appfuel\View\ViewInterface,
    Appfuel\View\ViewCompositor,
    Appfuel\View\FileViewTemplate,
    Appfuel\Html\HtmlPage,
    Appfuel\Html\HtmlPageConfiguration,
    Appfuel\Html\Tag\HtmlTagFactory,
    Appfuel\Html\Tag\HtmlTagFactoryInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 */
class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @param   MvcContextInterface     $context
     * @param   MvcRouteDetailInterface $detail
     * @return  null
     */
    public function setupView(MvcContextInterface $context,
                              MvcRouteDetailInterface $route,
                             $format = null)
    {
        if (null === $format) {
            $format = '';
        }

        if (! $route->isViewDisabled() && ! $route->isManualView()) {
            $context->setViewFormat($format);
            $view = $this->createTemplate($format);
            $context->setView($view);
            if ('html' === $format) {
                if ($route->isViewPackage()) {
                    $config = $this->createHtmlPageConfiguration(
                        AF_RESOURCE_URL, AF_IS_RESOURCE_BUILD
                    );
                    $config->applyView($route->getViewPackage(), $view);
                }
            }
        }
    }

    /**
     * @throws  DomainException
     * @param   string  $format
     * @return  ViewInterface
     */
    public function createTemplate($format)
    {
        switch ($format) {
            case 'html': $template = $this->createHtmlPage();       break;
            case 'csv' : $template = $this->createCsvTemplate();    break;
            case 'json': $template = $this->createAjaxTemplate();   break;
            case 'text': $template = $this->createViewTemplate();   break;
            default: 
                $template = $this->createViewTemplate();
        }

        return $template;
    }

    /**
     * @param   HtmlTagFactoryInterface $factory
     * @return  HtmlPage
     */
    public function createHtmlPage(HtmlTagFactoryInterface $factory = null)
    {
        if (null === $factory) {
            $factory = $this->createHtmlTagFactory();
        }
        return new HtmlPage();
    }

    /**
     * @return  HtmlTagFactory
     */
    public function createHtmlTagFactory()
    {
        return new HtmlTagFactory();
    }

    /**
     * @return  HtmlPageConfiguration
     */
    public function createHtmlPageConfiguration($url, $isBuild)
    {
        return new HtmlPageConfiguration($url, $isBuild);
    }

    /**
     * @return  AjaxTemplate
     */
    public function createAjaxTemplate()
    {
        return new AjaxTemplate();
    }

    /**
     * @return  ViewTemplate
     */
    public function createViewTemplate()
    {
        return new ViewTemplate();
    }

    /**
     * Create a standard view template with a csv compositor instead of 
     * a file compositor.
     * @return  CsvTemplate
     */
    public function createCsvTemplate()
    {
        return new CsvTemplate();
    }
}

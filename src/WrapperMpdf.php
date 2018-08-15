<?php declare(strict_types=1);

namespace WrapperMpdf;

use GeneralForm\ITemplatePath;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Nette\Application\UI\Control;
use Nette\DI\Container;
use Nette\Localization\ITranslator;
use Psr\Log\AbstractLogger;


/**
 * Class WrapperMpdf
 *
 * @author  geniv
 * @package WrapperMpdf
 */
class WrapperMpdf extends Control implements ITemplatePath
{
    /** @var Mpdf */
    private $mpdf;
    /** @var ITranslator */
    private $translator;
    /** @var string */
    private $templatePath, $templatePathHeader, $templatePathFooter, $templatePathStyle;
    /** @var array */
    private $variableTemplate = [];
    /** @var array */
    private $parameters = [];


    /**
     * WrapperMpdf constructor.
     *
     * @param Container        $container
     * @param ITranslator|null $translator
     */
    public function __construct(Container $container, ITranslator $translator = null)
    {
        parent::__construct();

        $parameters = $container->getParameters();

        $defaultFontConfig = (new FontVariables())->getDefaults();
        // default configure
        $this->parameters['config'] = [
            'tempDir'  => $parameters['tempDir'],
            'mode'     => 'utf-8',
            'format'   => 'A4',
            'fontdata' => $defaultFontConfig['fontdata'],
        ];

        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/FrontEditor.latte'; // set path
    }


    /**
     * Set template path.
     *
     * @param string $path
     */
    public function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
    }


    /**
     * Set template path header.
     *
     * @param string $path
     */
    public function setTemplatePathHeader(string $path)
    {
        $this->templatePathHeader = $path;
    }


    /**
     * Set template path footer.
     *
     * @param string $path
     */
    public function setTemplatePathFooter(string $path)
    {
        $this->templatePathFooter = $path;
    }


    /**
     * Set template path style.
     *
     * @param string $path
     */
    public function setTemplatePathStyle(string $path)
    {
        $this->templatePathStyle = $path;
    }


    /**
     * Set backtrack limit.
     *
     * @param string $limit
     */
    public function setBacktrackLimit(string $limit)
    {
        ini_set('pcre.backtrack_limit', $limit);
    }


    /**
     * Add variable template.
     *
     * @param string $name
     * @param        $values
     */
    public function addVariableTemplate(string $name, $values)
    {
        $this->variableTemplate[$name] = $values;
    }


    /**
     * Set logger.
     *
     * @param AbstractLogger $logger
     */
    public function setLogger(AbstractLogger $logger)
    {
        $this->parameters['logger'] = $logger;
    }


    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->parameters['title'] = $title;
    }


    /**
     * Add font directory.
     *
     * @param string $directory
     */
    public function addFontDirectory(string $directory)
    {
        $this->parameters['FontDirectory'][] = $directory;
    }


    /**
     * Add font.
     *
     * @param string $name
     * @param array  $styles
     */
    public function addFont(string $name, array $styles)
    {
        $this->parameters['config']['fontdata'][$name] = $styles;
    }


    /**
     * Set format.
     *
     * @param $format
     */
    public function setFormat($format)
    {
        $this->parameters['config']['format'] = $format;
    }


    /**
     * Set mode.
     *
     * @param $mode
     */
    public function setMode($mode)
    {
        $this->parameters['config']['mode'] = $mode;
    }


    /**
     * Add config.
     *
     * @param string $name
     * @param        $values
     */
    public function addConfig(string $name, $values)
    {
        $this->parameters['config'][$name] = $values;
    }


    /**
     * Set showImageErrors.
     *
     * @param bool $state
     */
    public function setShowImageErrors(bool $state)
    {
        $this->parameters['showImageErrors'] = $state;
    }


    /**
     * Set debug.
     *
     * @param bool $state
     */
    public function setDebug(bool $state)
    {
        $this->parameters['debug'] = $state;
    }


    /**
     * Init mpdf.
     *
     * @throws \Mpdf\MpdfException
     */
    private function initMpdf()
    {
        $this->mpdf = new Mpdf($this->parameters['config']);

        // set showImageErrors
        if (isset($this->parameters['showImageErrors'])) {
            $this->mpdf->showImageErrors = $this->parameters['showImageErrors'];
        }

        // set debug
        if (isset($this->parameters['debug'])) {
            $this->mpdf->debug = $this->parameters['debug'];
        }

        // set font directory
        if (isset($this->parameters['FontDirectory']) && $this->parameters['FontDirectory']) {
            foreach ($this->parameters['FontDirectory'] as $item) {
                $this->mpdf->AddFontDirectory($item);
            }
        }

        // set pdf title
        if (isset($this->parameters['title'])) {
            $this->mpdf->SetTitle($this->parameters['title']);
        }

        // set logger
        if (isset($this->parameters['logger'])) {
            $this->mpdf->setLogger($this->parameters['logger']);
        }
    }


    /**
     * Make header template.
     */
    private function makeHeaderTemplate()
    {
        if ($this->templatePathHeader) {
            $header = $this->createTemplate();
            $header->addFilter(null, 'LatteFilters::common');
            $header->setTranslator($this->translator);
            $header->setFile($this->templatePathHeader);

            // add user defined variable
            foreach ($this->variableTemplate as $name => $value) {
                $header->$name = $value;
            }
            $this->mpdf->SetHTMLHeader($header);
        }
    }


    /**
     * Make footer template.
     */
    private function makeFooterTemplate()
    {
        if ($this->templatePathFooter) {
            $footer = $this->createTemplate();
            $footer->addFilter(null, 'LatteFilters::common');
            $footer->setTranslator($this->translator);
            $footer->setFile($this->templatePathFooter);

            // add user defined variable
            foreach ($this->variableTemplate as $name => $value) {
                $footer->$name = $value;
            }
            $this->mpdf->SetHTMLFooter($footer);
        }
    }


    /**
     * Get style template.
     *
     * @return string
     */
    private function getStyleTemplate(): string
    {
        $style = '';
        if ($this->templatePathStyle) {
            $style = $this->createTemplate();
            $style->addFilter(null, 'LatteFilters::common');
            $style->setTranslator($this->translator);
            $style->setFile($this->templatePathStyle);

            // add user defined variable
            foreach ($this->variableTemplate as $name => $value) {
                $style->$name = $value;
            }
        }
        return (string) $style;
    }


    /**
     * Render.
     *
     * @param bool        $preview
     * @param string|null $fileName
     * @param string|null $destination
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \Nette\Application\AbortException
     */
    public function render(bool $preview = false, string $fileName = null, string $destination = null)
    {
        $this->initMpdf();

        // main template
        $template = $this->getTemplate();
        $template->addFilter(null, 'LatteFilters::common');
        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);

        // header
        $this->makeHeaderTemplate();
        // footer
        $this->makeFooterTemplate();
        // style
        $template->style = $this->getStyleTemplate();   // {$style|noescape}

        // add user defined variable
        foreach ($this->variableTemplate as $name => $value) {
            $template->$name = $value;
        }

        if ($preview) {
            // preview
            $template->render();
        } else {
            $this->mpdf->WriteHTML($template);
            return $this->mpdf->Output($fileName, $destination);
        }
        $this->presenter->terminate();
    }
}

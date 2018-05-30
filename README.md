Wrapper mpdf
============

mpdf package:
- https://github.com/mpdf/mpdf


mpdf doc:
- https://mpdf.github.io/
- https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
- https://mpdf.github.io/css-stylesheets/supported-css.html
- https://mpdf.github.io/what-else-can-i-do/backgrounds-borders.html


mpdf stackoverflow:
- https://stackoverflow.com/questions/tagged/mpdf


Installation
------------

```sh
$ composer require geniv/nette-wrapper-mpdf
```
or
```json
"geniv/nette-wrapper-mpdf": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"geniv/nette-general-form": ">=1.0.0",
"mpdf/mpdf": ">=7.1.0"
```

Include in application
----------------------

neon configure:
```neon
services:
    - WrapperMpdf\WrapperMpdf
#    - WrapperMpdf\Logger
```

presenter define:
```php
public function createComponentWrapperMpdf(WrapperMpdf $wrapperMpdf): WrapperMpdf
{
    $wrapperMpdf->setTemplatePath(__DIR__ . '/templates/Project/pdf.latte');
    $wrapperMpdf->setTemplatePathHeader(__DIR__ . '/templates/pdfHeader.latte');
    $wrapperMpdf->setTemplatePathFooter(__DIR__ . '/templates/pdfFooter.latte');
    $wrapperMpdf->setTemplatePathStyle(__DIR__ . '/templates/pdfStyle.latte');
    
    $wrapperMpdf->setTitle('Titulek');
//    $wrapperMpdf->setFormat('A4-L');
//    $wrapperMpdf->setFormat('A4');
    $wrapperMpdf->addFontDirectory(__DIR__ . '/../components/mPDF/ttfonts');
    $wrapperMpdf->addFont('roboto', [
        'R'  => 'Roboto-Regular.ttf',
        'B'  => 'Roboto-Bold.ttf',
        'I'  => 'Roboto-Italic.ttf',
        'BI' => 'Roboto-BoldItalic.ttf',
    ]);

    $wrapperMpdf->addConfig('setAutoBottomMargin', 'stretch');
    $wrapperMpdf->addConfig('autoMarginPadding', -9);

//    $wrapperMpdf->setLogger($logger);
//    $wrapperMpdf->setShowImageErrors(true);
//    $wrapperMpdf->setDebug(true);

    return $wrapperMpdf;
}
```

presenter usage:
```php
public function actionPdf()
{
    $wrapper = $this['wrapperMpdf'];
    $wrapper->setTemplatePath(__DIR__ . '/templates/pdf.latte');

//    $wrapper->setFormat('A4-L');
    $wrapper->setFormat('A4');

    $title = 'title of page';
    $wrapper->setTitle($title);
    $wrapper->addVariableTemplate('title', $title);

    $wrapper->render();
//    $wrapper->render(true);   // preview
}
```

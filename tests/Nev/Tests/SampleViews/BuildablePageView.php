<?php

namespace Nev\Tests\SampleViews;

use Nev\Html;
use Nev\View;

final class BuildablePageView extends View {
    use Html;

    protected $body = '';
    protected $scripts = [];
    protected $cssFiles = [];

    public function render() { ?>

        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
            <?= $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
                ...$this->cssFiles
            ) ?>
            <title>Hello Nev</title>
        </head>
        <body>

        <?= self::draw($this->body) ?>

        <?= $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js",
            "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js",
            ...$this->scripts
        ) ?>

        </body>
        </html>
        <?
    }
}
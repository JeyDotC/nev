<?php
namespace Nev\Tests\SampleViews;

use Nev\View;

/**
 * Description of BasicView
 *
 * @author Win 10
 */
class BasicView extends View {
    use \Nev\Html;

    protected function render() {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
                <? $this->css('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css') ?>
                
                <title>Hello Nev</title>
            </head>
            <body>
                <h1>Hello world.</h1>
                <? $this->js("https://code.jquery.com/jquery-3.3.1.slim.min.js")
                        ->js("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js")
                        ->js("https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js");
                ?>
            </body>
        </html>
        <?
    }
}

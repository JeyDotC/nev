<?php

namespace Nev\Tests;

use Nev\Tests\SampleViews\BasicView;
use Nev\Tests\SampleViews\ChildView;
use Nev\Tests\SampleViews\SomeViewModel;
use Nev\View;
use PHPUnit\Framework\TestCase;
use const DATA_DIR;

/**
 * Description of ViewTest
 *
 * @author Win 10
 */
class ViewTest extends TestCase {

    /**
     * 
     * @dataProvider displayProvider
     * 
     * @param BasicView $view
     * @param mixed $model
     * @param string $expectedResult
     */
    public function testDisplay(View $view, $model, $expectedResult) {
        // Act
        ob_start();
        $view->display($model);
        $result = ob_get_clean();
       
//        $file = DATA_DIR . "/" . str_replace(DIRECTORY_SEPARATOR, '', get_class($view)) . '.html';
//        file_put_contents($file, $result);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 
     * @dataProvider displayProvider
     * 
     * @param BasicView $view
     * @param mixed $model
     * @param string $expectedResult
     */
    public function testDisplayAndGet(View $view, $model, $expectedResult) {
        // Act
        $result = $view->displayAndGet($model);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testShow() {
        // Arrange
        $expectedResult = file_get_contents(DATA_DIR . '/BasicView-display.html');

        // Act
        ob_start();
        BasicView::show();
        $result = ob_get_clean();

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testCreate() {
        // Act
        $result = BasicView::create();

        // Assert
        $this->assertInstanceOf(View::class, $result);
    }

    public function displayProvider(): array {
        return [
            BasicView::class => [
                new BasicView(),
                null,
                file_get_contents(DATA_DIR . '/BasicView-display.html')
            ],
            'ChildVew extends ParentView' => [
                new ChildView(),
                new SomeViewModel(1, 'John Doe'),
                file_get_contents(DATA_DIR . '/ChildView-display.html')
            ]
        ];
    }

}

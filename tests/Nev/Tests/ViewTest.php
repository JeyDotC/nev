<?php

namespace Nev\Tests;

use Nev\Tests\SampleViews\BasicView;
use Nev\Tests\SampleViews\ChildView;
use Nev\Tests\SampleViews\CompositeView;
use Nev\Tests\SampleViews\SomeViewModel;
use Nev\View;
use PHPUnit\Framework\TestCase;

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
     * @param View $view
     * @param string $expectedResult
     */
    public function testDisplay(View $view, $expectedResult) {
        // Act
        ob_start();
        $view->display();
        $result = ob_get_clean();
       
        // Assert
        $this->assertXmlStringEqualsXmlFile($expectedResult, $result);
    }

    /**
     * 
     * @dataProvider displayProvider
     * 
     * @param View $view
     * @param string $expectedResult
     */
    public function testDisplayAndGet(View $view, $expectedResult) {
        // Act
        $result = $view->displayAndGet();

        // Assert
        $this->assertXmlStringEqualsXmlFile($expectedResult, $result);
    }

    public function testShow() {
        // Arrange
        $expectedResult = $this->getExpectedData('BasicView-display.html');

        // Act
        ob_start();
        BasicView::show();
        $result = ob_get_clean();

        // Assert
        $this->assertXmlStringEqualsXmlFile($expectedResult, $result);
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
                $this->getExpectedData('BasicView-display.html')
            ],
            'ChildVew extends ParentView' => [
                new ChildView(new SomeViewModel(1, 'John Doe')),
                $this->getExpectedData('ChildView-display.html')
            ],
            'Composite View' => [
                new CompositeView([
                    new SomeViewModel(1, 'Johny', 'Be good'),
                    new SomeViewModel(2, 'Jenny', 'Be cool!'),
                    new SomeViewModel(3, 'Jinny', 'Be smooth'),
                ]),
                $this->getExpectedData('CompositeView-display.html')
            ]
        ];
    }

    private function getExpectedData(string $fileName): string {
        return __DIR__ . "/../../data/$fileName";
    }
}

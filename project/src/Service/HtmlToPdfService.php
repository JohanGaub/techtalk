<?php

namespace App\Service;

use HeadlessChromium\BrowserFactory;

class HtmlToPdfService
{
    public static function render(string $html): void
    {
//        $browserFactory = new BrowserFactory();
//        $browserFactory->setOptions([
//            'windowSize' => [1920, 1000],
//            'userDataDir' => '../public',
//        ]);
//        $browser = $browserFactory->createBrowser();

                $browser = (new BrowserFactory())->createBrowser([
                    'windowSize' => [1920, 1000],
                    'userDataDir' => '../public',
                ]);


        try {
            // creates a new page and navigate to an URL
            $page = $browser->createPage();
            $page->navigate('http://example.com')->waitForNavigation();

            // get page title
            $pageTitle = $page->evaluate('document.title')->getReturnValue();

            // screenshot - Say "Cheese"! 😄
            $page->screenshot()->saveToFile('/foo/bar.png');

            // pdf
            $page->pdf(['printBackground' => false])->saveToFile('/foo/bar.pdf');
        } finally {
            // bye
            $browser->close();
        }




        /////////////////////////////////
//        try {
//            // creates a new page and navigate to an URL
//            $page = $browser->createPage();
//            //            $page->navigate('http://example.com')->waitForNavigation();
//            //            // get page title
//            //            $pageTitle = $page->evaluate('document.title')->getReturnValue();
//            $page->setHtml($html);
//            // screenshot
//            $page->screenshot()->saveToFile('../../assets/upload/pdf/example.png');
//
//            //            $page->pdf(['printBackground' => false])->saveToFile('/foo/bar.pdf');
//            base64_decode(
//                $page
//                ->pdf([
//                    'printBackground' => true
//                ])
//                ->getBase64()
//                ->saveToFile('../../assets/upload/example.pdf')
//            );
//
//
//        } finally {
//            // bye
//            $browser->close();
//        }
////////////////////////////////////
//        $page = $browser->createPage();
//
//        $page->setHtml($html);
//
//        return base64_decode(
//            $page->pdf([
//                'printBackground' => true
//            ])->getBase64()
//        );
    }
}

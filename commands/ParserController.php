<?php

namespace app\commands;

use app\models\Page;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class ParserController extends Controller
{
    public function actionIndex()
    {
        $xml = file_get_contents(Yii::$app->params['parser']['url']);
        $dom = \DOMDocument::loadXML($xml);
        $items = $dom->getElementsByTagName('item');
        foreach ($items as $item) {
            /** @var \DOMElement $item */
            $childNodes = $item->childNodes;
            $item = [];
            foreach ($childNodes as $childNode) {
                /** @var \DOMElement $childNode */
                $item[$childNode->nodeName] = $childNode->nodeValue;
            }
            $item['link'] = substr($item['link'], 0, 1023);
            if (0 < strpos($item['link'], 'finance.yahoo.com')) {
                if (is_null(Page::findBySource($item['link']))) {
                    $page = new Page([
                        'title' => $item['title'],
                        'source' => $item['link']
                    ]);
                    $page->parseSource();
                    if (!$page->save()) {
                        throw new Exception('Page don\'t saving!' . PHP_EOL . $page->getErrorSummary(true));
                    }
                }
            }
            sleep(rand(Yii::$app->params['parser']['sleep'][0], Yii::$app->params['parser']['sleep'][1]));
        }
        return ExitCode::OK;
    }
}

<?php

namespace app\models;

use Exception;
use simplehtmldom\HtmlDocument;
use simplehtmldom\HtmlWeb;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property string $source
 * @property string $title
 * @property string $description
 * @property string $body
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Page extends ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @param string $source
     * @return array|ActiveRecord|null
     */
    public static function findBySource($source)
    {
        return self::find()->where(['source' => $source])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'filter', 'filter' => '\yii\helpers\Html::encode'],
            ['title', 'filter', 'filter' => 'trim'],
            ['title', 'required'],
            ['title', 'string', 'max' => 255],

            ['body', 'filter', 'filter' => 'trim'],
            ['body', 'required'],
            ['body', 'string'],

            ['source', 'filter', 'filter' => '\yii\helpers\Html::encode'],
            ['source', 'filter', 'filter' => 'trim'],
            ['source', 'required'],
            ['source', 'string', 'max' => 1023],
            ['source', 'unique'],

            ['description', 'filter', 'filter' => '\yii\helpers\Html::encode'],
            ['description', 'filter', 'filter' => 'trim'],
            ['description', 'required'],
            ['description', 'string', 'max' => 1023],
            ['description', 'default', 'value' => null]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source' => 'Source',
            'title' => 'Title',
            'description' => 'Description',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function parseSource($parseBody = true)
    {
        $client = new HtmlWeb();
        $html = $client->load($this->source);

        $description = $html->find('meta[name=description]', 0);
        if (is_null($description)) {
            throw new Exception('Maybe, you will banned');
        }
        $description = $html->find('meta[name=description]', 0)->content;
        $this->description = StringHelper::truncate($description, 1023, '');

        $body = $html->find('.caas-body', 0)->innertext;
        if (is_null($body)) {
            throw new Exception('Maybe, you will banned');
        }
        $this->body = $body;
        if ($parseBody) {
            $this->parseBody();
        }
    }

    public function parseBody()
    {
        // Remove all link
        $this->body = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $this->body);
        $html = new HtmlDocument($this->body);
        // Upload image
        $imgs = $html->find('img');
        $uploadDir = \Yii::getAlias('@app/web/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir);
        }
        foreach ($imgs as $img) {
            /** @var \simplehtmldom\HtmlNode $img */
            $img->removeAttribute('data-src');
            $img->removeAttribute('class');
            $img->alt = $this->title;
            $fileName = md5($img->src);
            $ch = curl_init($img->src);
            $fp = fopen($uploadDir . '/' . $fileName, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            try {
                $getimagesize = getimagesize($uploadDir . '/' . $fileName);
                $extension = explode('/', $getimagesize['mime'])[1];
                if ('jpeg' == $extension) {
                    $extension = 'jpg';
                }
                $img->src = '/uploads/' . $fileName . '.' . $extension;
                rename($uploadDir . '/' . $fileName, $uploadDir . '/' . $fileName . '.' . $extension);
            } catch (Exception $e) {

            }
            sleep(rand(Yii::$app->params['parser']['sleep'][0], Yii::$app->params['parser']['sleep'][1]));
        }
        // Replace figure
        $figures = $html->find('figure');
        foreach ($figures as $key => $figure) {
            /** @var \simplehtmldom\HtmlNode $figure */
            $figure->outertext = $imgs[$key];
        }
        $this->body = $html->save();
        $this->body = preg_replace('#<div.*?>(.*?)</div>#i', '', $this->body);
    }
}

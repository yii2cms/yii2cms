<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;


class Migration extends \yii\db\Migration
{

    protected function longText()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext');
    }
}

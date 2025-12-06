<?php 
echo "<?php\n";
?> 
 
namespace <?= $generator->ns ?>;

use Yii;
 
class <?= $className ?> extends \app\modules\core\classes\ActiveRecord
{

<?php if (!empty($enum)): ?>
    
<?php
    foreach($enum as $columnName => $columnData) {
        foreach ($columnData['values'] as $enumValue){
            echo '    const ' . $enumValue['constName'] . ' = \'' . $enumValue['value'] . '\';' . PHP_EOL;
        }
    }
endif
?>
 
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

     
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
    }
 
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>
 
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?> 

<?php if ($enum): ?>
<?php     foreach ($enum as $columnName => $columnData): ?> 
     
    public static function <?= $columnData['funcOptsName'] ?>()
    {
        return [
<?php         foreach ($columnData['values'] as $k => $value): ?>
<?php
        if ($generator->enableI18N) {
            echo '            self::' . $value['constName'] . ' => Yii::t(\'' . $generator->messageCategory . '\', \'' . $value['value'] . "'),\n";
        } else {
            echo '            self::' . $value['constName'] . ' => \'' . $value['value'] . "',\n";
        }
    ?>
<?php         endforeach; ?>
        ];
    }
<?php     endforeach; ?>
<?php     foreach ($enum as $columnName => $columnData): ?>
 
    public function <?= $columnData['displayFunctionPrefix'] ?>()
    {
        return self::<?= $columnData['funcOptsName'] ?>()[$this-><?=$columnName?>];
    }
<?php         foreach ($columnData['values'] as $enumValue): ?> 
    
    public function <?= $columnData['isFunctionPrefix'] . $enumValue['functionSuffix'] ?>()
    {
        return $this-><?= $columnName ?> === self::<?= $enumValue['constName'] ?>;
    }

    public function <?= $columnData['setFunctionPrefix'] . $enumValue['functionSuffix'] ?>()
    {
        $this-><?= $columnName ?> = self::<?= $enumValue['constName'] ?>;
    }
<?php         endforeach; ?>
<?php     endforeach; ?>
<?php endif; ?>
}

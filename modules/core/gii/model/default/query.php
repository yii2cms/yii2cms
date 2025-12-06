<?php 

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?> 

namespace <?= $generator->queryNs ?>;

class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    
    public function all($db = null)
    {
        return parent::all($db);
    }
  
    public function one($db = null)
    {
        return parent::one($db);
    }
}

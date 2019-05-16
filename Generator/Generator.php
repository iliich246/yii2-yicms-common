<?php

namespace Iliich246\YicmsCommon\Generator;

use Yii;
use yii\base\Component;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;

/**
 * Class Generator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Generator extends Component
{
    /** @var AbstractConfigurableModule|YicmsModuleInterface  */
    private $yicmsModule;

    private $generatorDir;

    /**
     * @param AbstractConfigurableModule|YicmsModuleInterface $yicmsModule
     * @param array $config
     */
    public function __construct($yicmsModule, $config = [])
    {
        parent::__construct($config);
        $this->yicmsModule = $yicmsModule;
    }

    /**
     *
     */
    public function generate()
    {
        $this->generatorDir = $this->yicmsModule->getModuleDir()
            . DIRECTORY_SEPARATOR
            . 'Views'
            . DIRECTORY_SEPARATOR
            . 'generator';

        if (!file_exists($this->generatorDir) && !is_dir($this->generatorDir)) {
            Yii::error('NO DIR!!!');
            return;
        }

        $yicmsDir = Yii::getAlias(CommonModule::getInstance()->yicmsLocation);


        if (!file_exists($yicmsDir))
            mkdir($yicmsDir);

        $moduleYicmsDir = $yicmsDir . DIRECTORY_SEPARATOR . $this->yicmsModule->getModuleName();

        if (!file_exists($moduleYicmsDir))
            mkdir($moduleYicmsDir);


        //throw new \yii\base\Exception(print_r($generatorDir, true)); ;

        $generatorDirs = scandir($this->generatorDir);
        unset($generatorDirs[0]);
        unset($generatorDirs[1]);

        $yicmsNamespace = CommonModule::getInstance()->yicmsNamespace . '\\' . $this->yicmsModule->getModuleName();

        foreach($generatorDirs as $dir) {

            $file = $this->generatorDir . DIRECTORY_SEPARATOR . $dir;

            if (file_exists($file) && is_dir($file)) {
                Yii::error($dir .' is dir');
                $this->shareDir($file, $yicmsNamespace . '\\' . $dir);
                continue;
            }

            if (file_exists($file))

            Yii::error($dir . ' is_File');

        }

        //Yii::error(print_r($dirs, true));
        Yii::error(print_r($generatorDirs, true));
    }

    /**
     * @param $directory
     * @param $namespace
     */
    private function shareDir($directory, $namespace)
    {
        $sharedDir = scandir($directory);

        unset($sharedDir[0]);
        unset($sharedDir[1]);

        foreach($sharedDir as $dir) {

            $file = $directory . DIRECTORY_SEPARATOR . $dir;

            if (file_exists($file) && is_dir($file)) {
                Yii::error($dir .' is dir');
                $this->shareDir($file, $namespace . '\\' . $dir);
                continue;
            }

            if (file_exists($file))
                $this->shareFile($file, $namespace . '\\' . $dir);
        }

        return;
    }

    /**
     * @param $file
     * @param $namespace
     */
    private function shareFile($file, $namespace)
    {
        Yii::error($file . ' is_File');
    }
}

<?php

namespace Iliich246\YicmsCommon\Base;

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
    /** @var string directory of generator files */
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
     * Method generates module user files
     * @return void
     */
    public function generate()
    {
        if (!$this->yicmsModule->isNeedGenerate()) return;

        $this->generatorDir = $this->yicmsModule->getModuleDir()
            . DIRECTORY_SEPARATOR
            . 'Views'
            . DIRECTORY_SEPARATOR
            . 'generator';

        if (!file_exists($this->generatorDir) && !is_dir($this->generatorDir)) {

            return;
        }

        $yicmsDir = Yii::getAlias(CommonModule::getInstance()->yicmsLocation);


        if (!file_exists($yicmsDir))
            mkdir($yicmsDir);

        $moduleYicmsDir = $yicmsDir . DIRECTORY_SEPARATOR . $this->yicmsModule->getModuleName();

        if (!file_exists($moduleYicmsDir))
            mkdir($moduleYicmsDir);

        $generatorDirs = scandir($this->generatorDir);
        unset($generatorDirs[0]);
        unset($generatorDirs[1]);

        $yicmsNamespace = CommonModule::getInstance()->yicmsNamespace . '\\' . $this->yicmsModule->getModuleName();

        foreach($generatorDirs as $dir) {
            $file = $this->generatorDir . DIRECTORY_SEPARATOR . $dir;
            $destinationDir = $moduleYicmsDir . DIRECTORY_SEPARATOR . $dir;

            if (file_exists($file) && is_dir($file)) {

                if (!file_exists($destinationDir))
                    mkdir($destinationDir);

                $this->shareDir($file, $yicmsNamespace . '\\' . $dir, $destinationDir);
                continue;
            }
        }
    }

    /**
     * Share generator folder
     * @param $directory
     * @param $namespace
     * @param $destinationDir
     */
    private function shareDir($directory, $namespace, $destinationDir)
    {
        $sharedDir = scandir($directory);

        unset($sharedDir[0]);
        unset($sharedDir[1]);

        foreach($sharedDir as $dir) {

            $file = $directory . DIRECTORY_SEPARATOR . $dir;

            if (file_exists($file) && is_dir($file)) {
                if (!file_exists($destinationDir . DIRECTORY_SEPARATOR . $dir))
                    mkdir($destinationDir . DIRECTORY_SEPARATOR . $dir);

                $this->shareDir($file,
                    $namespace . '\\' . $dir,
                    $destinationDir . DIRECTORY_SEPARATOR . $dir);

                continue;
            }

            if (file_exists($file))
                $this->shareFile($file, $namespace, $destinationDir, $dir);
        }

        return;
    }

    /**
     * Write file to destination
     * @param $file
     * @param $namespace
     * @param $destinationDir
     * @param $fileName
     */
    private function shareFile($file, $namespace, $destinationDir, $fileName)
    {
        $resource = new \SplFileObject($file, 'r');

        $lineNumber = 0;
        $result = '';
        $insertNamespace = true;

        while(!$resource->eof()) {

            $line = $resource->fgets();

            if ($lineNumber == 0 && preg_match("/template/", $line)) {
                $line = '<?php' . PHP_EOL;
                $insertNamespace = false;
            }

            if ($insertNamespace && $lineNumber > 1 && preg_match("/namespace/", $line)) {
                $line = '';
                $line = 'namespace ' . $namespace .';' . PHP_EOL;
                $insertNamespace = false;
            }

            $result .= $line;

            $lineNumber++;
            if ($lineNumber > 50000) break;
        }

        $destinationFile = fopen($destinationDir . DIRECTORY_SEPARATOR . $fileName, "w");

        fwrite($destinationFile, $result);

        fclose($destinationFile);
    }
}

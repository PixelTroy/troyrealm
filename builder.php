<?php
/* Parse all params to params array. */
#error_reporting(0);
if(!isset($argv[2])) die("Missing params. example : \n php builder.php dockerName=easysoft/zdoo#tag=6.1#package=./zdoo.6.1.php7.2.zip config/zdoo.php\n");
parse_str(str_replace('#', '&', $argv[1]), $params);

/* check parameters */
if(!isset($params['dockerName'])) die("dockerName is invalid!");
if(!isset($params['tag'])) die("tag is invalid!");
if(!isset($params['package'])) die("package is invalid!");
if(!isset($params['save'])) $params['save'] = false;
if(!isset($params['type'])) $params['type'] = 'lamp';
if(!isset($params['ssl']))  $params['ssl']  = false;
extract($params);

/* Get basename of zip package. */
$packageBasename = dockerBuilder::getBasenameFromZip($package);

$builder    = new dockerBuilder($dockerName, $type);
$dockerPath = $builder->dockerPath;

include $argv[2];

if(isset($params['debug'])) $builder->debug = $debug;
$builder->build($dockerName, $tag, $commands, $save);

/**
 * The baseBuilder class file of docker image
 * 
 * @copyright Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author    chunsheng wang <chunsheng@cnezsoft.com> 
 * @package   
 * @license   LGPL
 * @version   $Id$
 * @Link      http://www.zentao.net
 */
class dockerBuilder
{
    public $basePath   = '';
    public $dockerPath = '';
    public $debug      = 0;

    public function __construct($dockerName, $basePackage = 'lamp')
    {
        $this->basePath   = dirname(__FILE__);
        $this->dockerPath = $this->initDockerPath($basePackage, $dockerName);
    }

    /**
     * Main build function
     * 
     * @param  string    $dockerName 
     * @param  string    $tag 
     * @param  array  $commands 
     * @param  string    $saveImage 
     * @access public
     * @return void
     */
    public function build($dockerName, $tag, $commands = array(), $saveImage)
    {
        $this->preBuild($commands['pre']);
        $this->buildDockerfile($commands['Dockerfile']);
        $this->buildInitfile($commands['init']);
        $buildResult = $this->execDockerBuild($dockerName, $tag);
        if(isset($saveImage) && $saveImage == 'true' ) $this->saveImage($dockerName, $tag);
    }
    
    /**
     * saveImage 
     * 
     * @param  string    $dockerName 
     * @param  string    $tag 
     * @access public
     * @return void
     */
    public function saveImage($dockerName, $tag)
    {
        $imagePath = $this->dockerPath . DIRECTORY_SEPARATOR .  $dockerName . '.tar';
        exec("docker save $dockerName:$tag -o $imagePath", $output, $resultCode);
        if(!$resultCode) 
        {
            echo 'Save image succcess.' . PHP_EOL;
            echo 'imagePath:' . $imagePath . PHP_EOL;
        }else{
            die('Save image failed!' . PHP_EOL);
        }
    }

    /**
     * execute docker build to make a docker image 
     * 
     * @param  string   $dockerName 
     * @param  string   $tag 
     * @access public
     * @return void
     */
    public function execDockerBuild($dockerName, $tag)
    {
        exec("docker build -t $dockerName:$tag {$this->dockerPath} 2>&1 > /tmp/build.log", $output, $resultCode);
		for($i=0;$i<count($output);$i++){
		    echo $output[$i] . PHP_EOL;
		}   
        if(!$resultCode) 
        {
            echo `docker images |grep $dockerName`;
            echo 'Build image succcess.' . PHP_EOL;
        }else{
            die('Build image failed!' . PHP_EOL);
        }
        return $resultCode;
    }

    /**
     * Create a docker path from base package.
     * 
     * @param  string    $basePackage 
     * @param  string    $dockerName 
     * @access public
     * @return void
     */
    public function initDockerPath($basePackage, $dockerName)
    {
        if(!is_dir($basePackage)) die("Package : $basePackage not exists.");
        $dockerPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $dockerName;
        if(is_dir($dockerPath)) `rm -rf $dockerPath`;
        $basePackage = $basePackage . DIRECTORY_SEPARATOR . '*';
        `mkdir -p $dockerPath && cp -r $basePackage $dockerPath`;
        return $dockerPath;
    }

    /**
     * Exec command before build image. 
     * 
     * @param  string     $commands 
     * @access public
     * @return void
     */
    public function preBuild($commands)
    {
        foreach($commands as $command) 
        {
            $this->showCommand($command);
            echo `$command`;
        }
    }

    /**
     * Build dockerfile with post commands.
     * 
     * @param  array    $commands 
     * @access public
     * @return bool
     */
    public function buildDockerfile($commands)
    {
        $dockerFile = $this->dockerPath . DIRECTORY_SEPARATOR . 'Dockerfile';
        $dockerFileInsertRow = $this->getDockerFileInsertRow($dockerFile);
        $rowNumber = (int)$dockerFileInsertRow;
        foreach($commands as $command)
        {
            $this->showCommand($command);
            $this->insertContentBySipecifiedRow($dockerFile, $rowNumber, $command);
            $rowNumber++; 
        }
    }

    /**
     * get Dockerfile start insert row number 
     * 
     * @param  string    $dockerFilePath 
     * @access public
     * @return void
     */
    public function getDockerFileInsertRow($dockerFilePath)
    {
        return trim(`grep -n "COPY" $dockerFilePath | tail -n 1 |awk -F ":" '{print $1}'`);
    }

    /**
     * insert the specified line into the file 
     * 
     * @param  string    $filePath 
     * @param  int       $rowNumber 
     * @param  string    $content 
     * @access public
     * @return void
     */
    public function insertContentBySipecifiedRow($filePath, $rowNumber, $content)
    {
        `sed -i '$rowNumber a $content' $filePath`;
    }

    /**
     * Build dockerfile with post commands.
     * 
     * @param  array    $commands 
     * @access public
     * @return bool
     */
    public function buildInitfile($commands)
    {
        $initFile  = $this->dockerPath . DIRECTORY_SEPARATOR . 'docker_init.sh';
        $rowNumber = 3;  
        foreach($commands as $command)
        {
            $this->showCommand($command);
            $this->insertContentBySipecifiedRow($initFile, $rowNumber, $command);
            $rowNumber++;
        }
    }

    /**
     * Get root dir name from zip.
     * 
     * @param  string    $package 
     * @access public
     * @return string
     */
    public function getBasenameFromZip($package)
    {
        exec("unzip -l $package", $filesList);
        $dirInfo = explode(" ", $filesList[3]);
        return rtrim(array_pop($dirInfo), '/');
    }

    /**
     * Print command if debug. 
     * 
     * @param  string    $command 
     * @access public
     * @return void
     */
    public function showCommand($command)
    {
        if($this->debug == 1) echo $command . "\n";
    }
}

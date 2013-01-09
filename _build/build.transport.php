<?php
$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

/* define package names */
define('PKG_NAME','Moxycart');
define('PKG_NAME_LOWER','moxycart');
define('PKG_VERSION','1.0');
define('PKG_RELEASE','pl');

// Change the path to the core/config
require_once(dirname(dirname(dirname(__FILE__))).'/core/config/config.inc.php');


require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
 
$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
 
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');



$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'moxycart',
    'parent' => '0',
    'controller' => 'index',
),'',true,true);

$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'moxy',
    'parent' => 'components',
    'description' => 'moxy.menu_desc',
    'action' => 1,
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);

$menu->addOne($action);

$vehicle = $builder->createVehicle($menu,array (
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));


// Copy over related files
$vehicle->resolve('file',array(
    'source' => MODX_BASE_PATH . PKG_NAME_LOWER . '/core/components/' . PKG_NAME_LOWER,
    'target' => "return MODX_CORE_PATH . 'components/';",
));


$builder->putVehicle($vehicle);



/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();
 
$tend= explode(" ", microtime());
$tend= $tend[1] + $tend[0];
$totalTime= sprintf("%2.4f s",($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

echo '<br/>Package complete. Check your '.MODX_CORE_PATH . 'packages/ directory for the newly created package.';

exit();

/*EOF*/
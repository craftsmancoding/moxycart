<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';

class StoreCreateManagerController extends ResourceCreateManagerController {

    public $resource;


    public function loadCustomCssJs() {
        parent::loadCustomCssJs();
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);

		//Add below for customization
        $this->addJavascript($assetsUrl . 'components/moxycart/js/productcontainer.js');
        $this->addHtml('
			<script type="text/javascript">
				isProductContainerCreate = true;
				
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
				});
			</script>');
			
        $this->addCss($assetsUrl.'components/moxycart/css/mgr.css');
    }    
    
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('container_new');
    }
    /**
     * Used to set values on the resource record sent to the template for derivative classes
     *
     * @return void
     */
//    public function prepareResource() {
/*
        $settings = $this->resource->getProperties('moxycart');
        if (empty($settings)) $settings = array();
        
        $defaultContainerTemplate = $this->modx->getOption('moxycart.default_container_template',$settings,false);
        if (empty($defaultContainerTemplate)) {
            // @var modTemplate $template 
            $template = $this->modx->getObject('modTemplate',array('templatename' => 'sample.ArticlesContainerTemplate'));
            if ($template) {
                $defaultContainerTemplate = $template->get('id');
            }
        }
        $this->resourceArray['template'] = $defaultContainerTemplate;

        $defaultArticleTemplate = $this->modx->getOption('moxycart.default_moxycart_template',$settings,false);
        if (empty($defaultArticleTemplate)) {
            // @var modTemplate $template
            $template = $this->modx->getObject('modTemplate',array('templatename' => 'sample.ArticleTemplate'));
            if ($template) {
                $defaultArticleTemplate = $template->get('id');
            }
        }
        $this->resourceArray['setting_moxycartTemplate'] = $defaultArticleTemplate;

        foreach ($settings as $k => $v) {
            $this->resourceArray['setting_'.$k] = $v;
        }
*/
//    }    
}
<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        parent::loadCustomCssJs();
        
        $mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        
		//Add below for customization
        $this->addJavascript($assets_url . 'components/moxycart/js/productcontainer.js');
    	$moxycart_connector_url = $assets_url.'components/moxycart/connector.php';

    	$this->addHtml('
			<script type="text/javascript">

                var connector_url = "'.$moxycart_connector_url.'";

				isProductContainerCreate = false;
				
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
				    MODx.load({
                        xtype: "articles-page-articles-container-create"
                    });

				});
			</script>');
			
        $this->addCss($assets_url.'components/moxycart/css/mgr.css');

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
    public function prepareResource() {
//        $this->resourceArray['template'] = 3;
//        $this->resourceArray['qty_alert'] = 666;
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
    }    
}
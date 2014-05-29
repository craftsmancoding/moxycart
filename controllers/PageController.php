<?php
/**
 * This HTML controller is what generates HTML pages (as opposed to JSON responses
 * generated by the other controllers).  The reason is testability: most of the 
 * manager app can be tested by $scriptProperties in, JSON out.  The HTML pages
 * generated by this controller end up being static HTML pages (well... ideally, 
 * anyway). 
 *
 * See http://stackoverflow.com/questions/10941249/separate-rest-json-api-server-and-client
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart
 */
namespace Moxycart;
class PageController extends BaseController {

    public $loadHeader = false;
    public $loadFooter = false;
    // GFD... this can't be set at runtime. See improvised addStandardLayout() function
    public $loadBaseJavascript = false; 
    // Stuff needed for interfacing with Moxycart API (mapi)
    public $client_config = array();
    
    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;
        // Set up any config data needed by the HTML client
        $this->client_config = array(
            'controller_url' => $this->config['controller_url']
        );
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/app.js');
    }
    
    //------------------------------------------------------------------------------
    //! Assets
    //------------------------------------------------------------------------------
    /**
     * Asset management main page
     *
     * @param array $scriptProperties
     */
    public function getAssets(array $scriptProperties = array()) {
        $Obj = new Asset($this->modx);
        $results = $Obj->all($scriptProperties);
//        return $results; exit;
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/assets.php');
    }
 
     public function getAssetCreate(array $scriptProperties = array()) {
        $Obj = new Asset($this->modx);
        $results = $Obj->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('asset/create.php');
    }    

    public function getAssetEdit(array $scriptProperties = array()) {
        $asset_id = (int) $this->modx->getOption('asset_id',$scriptProperties);
        $Obj = new Asset($this->modx);    
        if (!$result = $Obj->find($asset_id)) {
            return $this->sendError('Page not found.');
        }
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('asset/edit.php');
    }

    
    //------------------------------------------------------------------------------
    //! Fields
    //------------------------------------------------------------------------------
    /**
     * Field Management main page
     * @param array $scriptProperties
     */
    public function getFields(array $scriptProperties = array()) {
        $Obj = new Field($this->modx);
        $results = $Obj->all($scriptProperties);
        //$debug = $Obj->all($scriptProperties,true);
        //print $debug; exit;
        $this->setPlaceholder('debug', $debug);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/fields.php');
    }
    
    public function getFieldCreate(array $scriptProperties = array()) {
        $Obj = new Field($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('field/create.php');
    }    

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getFieldEdit(array $scriptProperties = array()) {
        $field_id = (int) $this->modx->getOption('field_id',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$result = $Obj->find($field_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('field','edit',array('field_id'=>$field_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('field/edit.php');
    }
    
    
    /**
     * 
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        return $this->fetchTemplate('main/index.php');
    }

    //------------------------------------------------------------------------------
    //! Options
    //------------------------------------------------------------------------------
    /**
     * Options Management
     * @param array $scriptProperties
     */
    public function getOptions(array $scriptProperties = array()) {
        $Obj = new OptionType($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('optiontype','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/options.php');
    }

    public function getOptionCreate(array $scriptProperties = array()) {
        $Obj = new OptionType($this->modx);    

        $scriptProperties['baseurl'] = self::url('optiontype','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('optiontype/create.php');
    }    

    /**
     * 
     */
    public function getOptionEdit(array $scriptProperties = array()) {    
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('optiontype','edit',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('optiontype/edit.php');
    }
    
    /**
     * 
     */
    public function getOptionTerms(array $scriptProperties = array()) {
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Invalid option type');
        }
        $Terms = new OptionTerm($this->modx);
        $Terms = $Terms->all(array('otype_id'=>$otype_id,'sort'=>'seq'));
        $scriptProperties['baseurl'] = self::url('optiontype','terms',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        $this->setPlaceholder('terms', $Terms);
        return $this->fetchTemplate('optiontype/terms.php');
    }


    //------------------------------------------------------------------------------
    //! Reports
    //------------------------------------------------------------------------------
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getReports(array $scriptProperties = array()) {
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/reports.php');
    }
    
    //------------------------------------------------------------------------------
    //! Reviews
    //------------------------------------------------------------------------------
    /**
     * Review Management
     * @param array $scriptProperties
     */
    public function getReviews(array $scriptProperties = array()) {

        $Obj = new Review($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::page('reviews');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/reviews.php');
    }

    /**
     *
     */
    public function getReviewEdit(array $scriptProperties = array()) {
        $review_id = (int) $this->modx->getOption('review_id',$scriptProperties);
        $Obj = new Review($this->modx);    
        if (!$result = $Obj->find($review_id)) {
            return $this->sendError('Page not found.');
        }
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('review/edit.php');
    }


    /**
     * 
     */
    public function getReviewCreate(array $scriptProperties = array()) {
        $Obj = new Review($this->modx);    
        //$scriptProperties['baseurl'] = self::url('review','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('review/create.php');
    }

    
    //------------------------------------------------------------------------------
    //! Settings
    //------------------------------------------------------------------------------
    /**
     * @param array $scriptProperties
     */
    public function getSettings(array $scriptProperties = array()) {

        return $this->fetchTemplate('main/settings.php');
     
    }
    
    
    public function getTest(array $scriptProperties = array()) {
        return $this->fetchTemplate('main/test.php');
    }
    
        
}
/*EOF*/
<?php
/**
 * Taxonomies Extra must be installed.
 */
namespace Moxycart;
class Taxonomy {
    public $modx;
    
    public function __construct(\modX &$modx) {
        $this->modx =& $modx;
    }
    
    /**
     * getTaxonomiesAndTerms : Taxonomies and Terms. BOOM.
     *
     * Get data structure describing taxonomy/terms for use in the form.
     * TODO: use the json caching here.
     * @return array containing structure compatible w Formbuilder: $data['Taxonomy Name']['Term Name'] = page ID
     */
    public function getTaxonomiesAndTerms() {
        $data = array();
        $c = $this->modx->newQuery('Taxonomy');
        $c->where(array('published'=>true,'class_key'=>'Taxonomy'));
        $c->sortby('menuindex','ASC');        
        if ($Ts = $this->modx->getCollection('Taxonomy', $c)) {
            foreach ($Ts as $t) {
/*
                $props = $t->get('properties');
                if (!isset($props['children'])) {
                    continue;
                }
*/
                $c = $this->modx->newQuery('Term');
                $c->where(array('published'=>true,'class_key'=>'Term','parent'=>$t->get('id')));
                $c->sortby('menuindex','ASC');        
                if ($Terms = $this->modx->getCollection('Term', $c)) {
                    foreach ($Terms as $term) {
                        $data[ $t->get('pagetitle') ][ $term->get('id') ] = $term->get('pagetitle');
                    }
                }
                // Plug the spot for the taxonomy?
                else {
                    $data[ $t->get('pagetitle') ] = array();
                }
            }
        }
        $this->modx->log(4,'getTaxonomiesAndTerms: '.print_r($data,true));
        return $data;
    }


}
/*EOF*/
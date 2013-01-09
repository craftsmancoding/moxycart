<?php
if(!class_exists('TemplateSelectInputRender')) {
    class TemplateSelectInputRender extends modTemplateVarInputRender {
        public function getTemplate() {
            return $this->modx->getOption('core_path').'components/moxycart/tv/input/tpl/templateselect.tpl';
        }
        public function process($value,array $params = array()) {
 
        }
    }
}
return 'TemplateSelectInputRender';
/*EOF*/
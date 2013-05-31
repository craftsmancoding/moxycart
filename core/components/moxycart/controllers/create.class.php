<?php
class ProductCreateManagerController extends ResourceCreateManagerController {
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
}
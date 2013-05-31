<?php
class ProductUpdateManagerController extends ResourceUpdateManagerController {
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
}
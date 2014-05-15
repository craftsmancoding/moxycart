<?php
/**
 * Model
 * This is a bit tricky because we already have an ORM layer: that Product class already
 * handles newObject, save, etc.  The getCollection 
 *
 * This model class should define a unified interface that define the graphs that represent
 * an object data in its entirety.
 */
namespace Moxycart\Model;
class Currency extends BaseModel {

    public $xclass = 'Currency';
    public $default_sort_col = 'name';

}
/*EOF*/
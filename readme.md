Moxycart Extra for MODx Revolution
=======================================

A turnkey FoxyCart eCommerce solution for MODX Revolution

**Author:** Everett Griffiths everett@craftsmancoding.com [Craftsman Coding](http://craftsmancoding.com/plugins/moxycart)

This plugin is being developed by CraftsmanCoding.com (http://craftsmancoding.com/)

WARNING: This project is currently in development! It should not be used by anyone yet!


------------------------------

## Technical Notes

### Manager Controllers

I broke with the standard MODX modus operandi here because it got restrictive with a package like this that has 
lots and lots of controllers required.  It meanders a bit in an attempt to reuse parts of the existing MODX framework.
Here's the quickie explanation of the routing that occurs in the CMPs:

1. The moxycart namespace (MODX's not PHP's) points to the package root.  The index action therefore looks there for the
index.class.php file and the IndexManagerController class.

2. The IndexManagerController class overrides the getInstance() function from the modManagerController class so we can 
instantiate our own controller classes.  This goes further than just overriding the getControllerClassName() function:
we implement some basic routing features here: the naked $method (e.g. index) gets a prefix depending on whether or not
post data is detected so that the actual function that will handle the request will be either "getIndex" or "postIndex".
This provides cleaner routing.

3. The next part of the routing is handled by the controllers/BaseController.php file when we override the render() function.
It was too restrictive to simply peg this to a hard-coded method ("process"), so we copy most of the function we 
are overriding but we instead call the method defined in the getInstance() function (e.g. "getIndex" or "postIndex").

4. The arguments passed to a controller method are from $scriptProperties and they include sanitized $_GET parameters.

The result is you can map requests more easily to a class and method, e.g.

    http://yoursite.com/manager/?a=93&class=Product --> maps to Product::getIndex()
    http://yoursite.com/manager/?a=93&class=Main&method=something --> maps to Main::getSomething()

### URLs for Controllers

Generating URLs to other manager controllers:

Moxycart\BaseController::url($class,$method='index',$args=array())



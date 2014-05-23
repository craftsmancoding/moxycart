Moxycart

This is a package for MODX Revolution 2.x that integrates the FoxyCart API, making MODX into a turnkey eCommerce solution.

Installation

Install this package via the MODX package manager.

What installation should accomplish:

* Generate your Foxycart API key
* Set up your store
* Set up your datafeed



Notes on the architecture:

* A MODX page with class_key=Store (i.e. a Custom Resource Class) acts as the parent container for products.
* Products live in their own dedicated table: this facilitates easier searching, reporting, importing, exporting, and more.
* Products can add their own custom fields (similar to page Template Variables).
* Unit tests exist for everything -- test driven development!
* Built using Repoman!


Authors: 

Everett Griffiths everett@craftsmancoding.com
Nick Hoag nick@craftsmancoding.com

Copyright 2014

Official Documentation: https://github.com/craftsmancoding/moxycart/wiki

Bugs and Feature Requests: https://github.com/craftsmancoding/moxycart

Questions: http://forums.modx.com


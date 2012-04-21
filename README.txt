
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Implementation
 * TODO


INTRODUCTION
------------

Current Maintainer: Travis Carden <http://drupal.org/user/236758>

Checklist API Provides a simple interface for modules to create fillable,
persistent checklists that track progress with completion times and users. See
checklistapi_example.module for an example implementation.


INSTALLATION
------------

Checklist API is installed in the usual way. See
http://drupal.org/documentation/install/modules-themes/modules-7.


IMPLEMENTATION
--------------

Checklists are declared as multidimensional arrays using
hook_checklistapi_checklist_info(). They can be altered using
hook_checklistapi_checklist_info_alter(). Checklist API handles creation of menu
items and permissions. Progress details are saved in one Drupal variable per
checklist. See checklistapi.api.php for more details.


TODO
----

These development tasks need to be completed for Checklist API:

 * Implement component sorting by weight.
 * Add tests.

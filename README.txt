
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Implementation


INTRODUCTION
------------

Current Maintainer: Travis Carden <http://drupal.org/user/236758>

Checklist API provides a simple interface for modules to create fillable,
persistent checklists that track progress with completion times and users. See
checklistapi_example.module for an example implementation.


INSTALLATION
------------

Checklist API is installed in the usual way. See
http://drupal.org/documentation/install/modules-themes/modules-8.


IMPLEMENTATION
--------------

Checklists are declared as multidimensional arrays using
hook_checklistapi_checklist_info(). They can be altered using
hook_checklistapi_checklist_info_alter(). Checklist API handles creation of menu
items and permissions. Progress details are saved in one config file per
checklist.

See checklistapi.api.php for more details.

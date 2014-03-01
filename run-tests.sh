#!/usr/bin/env sh

# @file
# Runs unit tests for Checklist API module.
#
# This script assumes that the module is installed in
# /path-to-drupal-docroot/modules/contrib/checklistapi.

cd ../../../core/
./vendor/bin/phpunit \
  --coverage-html ../modules/contrib/checklistapi/coverage \
  ../modules/contrib/checklistapi/tests/Drupal/checklistapi/Tests/

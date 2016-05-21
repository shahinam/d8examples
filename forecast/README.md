# Weather forecast

Add a block which shows current days weather forecast.

## Block API
Demonstrates basic block API.

## Composer Merge Plugin
Demonstrates, how to use composer to get dependencies downloaded in root vendor folder.

Drupal 8.1.x and beyond ships with composer.json where "composer-merge-plugin" is already there. We just need to add
the paths where we want to search for additional composer.json files.

Prior to 8.1.x, we can add the depencency like
``"wikimedia/composer-merge-plugin": "~1.3"``
in require section of composer.json and add the setting to look for addtional composer.json files. like

```
"merge-plugin": {
  "include": [
    "core/composer.json",
    "modules/custom/*/composer.json"
  ],
  "recurse": false,
  "replace": false,
  "merge-extra": false
```

Add the above setting in extra section of composer.json

Here is example composer.json file from Drupal 8.1

```
{
    "name": "drupal/drupal",
    "description": "Drupal is an open source content management platform powering millions of websites and applications.",
    "type": "project",
    "license": "GPL-2.0+",
    "require": {
        "composer/installers": "^1.0.21",
        "wikimedia/composer-merge-plugin": "~1.3"
    },
    "replace": {
        "drupal/core": "~8.1"
    },
    "minimum-stability": "dev",
    "prefer-stable": true,
    "config": {
        "preferred-install": "dist",
        "autoloader-suffix": "Drupal8"
    },
    "extra": {
        "_readme": [
            "By default Drupal loads the autoloader from ./vendor/autoload.php.",
            "To change the autoloader you can edit ./autoload.php."
        ],
        "merge-plugin": {
            "include": [
                "core/composer.json",
                "modules/custom/*/composer.json"
            ],
            "recurse": false,
            "replace": false,
            "merge-extra": false
        }
    },
    "autoload": {
        "psr-4": {
            "Drupal\\Core\\Composer\\": "core/lib/Drupal/Core/Composer"
        }
    },
    "scripts": {
        "pre-autoload-dump": "Drupal\\Core\\Composer\\Composer::preAutoloadDump",
        "post-autoload-dump": "Drupal\\Core\\Composer\\Composer::ensureHtaccess",
        "post-package-install": "Drupal\\Core\\Composer\\Composer::vendorTestCodeCleanup",
        "post-package-update": "Drupal\\Core\\Composer\\Composer::vendorTestCodeCleanup"
    }
}

```

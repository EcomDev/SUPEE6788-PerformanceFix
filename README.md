# EcomDev_AdminPatchPerformance
Fix for SUPEE6788 that allows better handling of variable checks, also it automatically can create an entry in block and variable tables, if appropriate configuration option is turned on.


## Requirements
* Magento 1.x with SUPEE6788 installed

## Installation

1. Add extension and firegento repositories in composer  

         "repositories": [
            {
              "type": "composer",
              "url": "http://packages.firegento.com"
            },
            {
              "type": "vcs",
              "url": "git@github.com:EcomDev/SUPEE6788-PerformanceFix.git"
            }
        ]

2. Add extension as a requirement

        "require": {
            "ecomdev/magento-performance-fix-supee-6788": "1.*"
        }

3. Run `composer install`

4. Clean Magento Cache

5. [Optional] Go to admin panel "System -> Configuration -> Admin -> Security" to enable autocreation of variable and block records with "Disallowed" status.

6. Enjoy and drink with me some beer on the next Magento event :)

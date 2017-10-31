# tiny-back
Crud generator for Symfony3

# tiny-back
Crud generator for Symfony3

INSTALL

STEP 1
[...]
"require": {
    [...]
    "zurd2/tiny-back" : "dev-master"
},
[...]
"repositories" : [{
    "type" : "vcs",
    "url" : "https://github.com/Zurd2/tiny-back.git"
}]
[...]

STEP 2
composer require zurd2/tiny-back

STEP 3
app/AppKernel.php

[...]
$bundles = [
    [...]
    new Zurd2\SmallCrudBundle\SmallCrudBundle(),
];
[...]

STEP 4
app/config/routing.yml

[...]
small_crud:
    resource: "@SmallCrudBundle/Controller/"
    type:     annotation

# CoreBundle
This is a Symfony Bundle compatible with Symfony 2.6+ which provides several features we 
consider core here at Sideclick.

## Installation

### Step 1: Add the following to the "require" section of composer.json

```
"sideclick/core-bundle": "dev-master"
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sc\CoreBundle\ScCoreBundle(),
    );
}
```
### Step 3: Install additional Bundles

This Bundle requires that other bundles be installed and configured, for the moment there is just one:
- https://github.com/dustin10/VichUploaderBundle

You will need ton install and configure these before being able to use this Bundle.



More documentation to come...

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


More documentation to come...

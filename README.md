# secret-keeper

Secret Keeper helps you load up various credential/secret files, typically YAML or JSON.

## Usage

Given a YAML file like this one, which we'll call `database.yml`:

```yaml
dev:
  name: mydevdatabase
  user: mydevdatabaseuser
  password: verylongandsecretstring
staging:
  name: mystagingdatabase
  user: mystagingdatabaseuser
  password: verylongandsecretstring
production:
  name: myproductiondatabase
  user: myproductiondatabaseuser
  password: verylongandsecretstring
```

Load it like so:

```php
<?php

use TomodomoCo\SecretKeeper;

$loader = new SecretKeeper('/absolute/path/to/secrets/');
$loader->setStage('dev');

$secrets = [
    [
        'file'   => 'database.yml',
        'type'   => 'yaml',
        'prefix' => 'database',
    ],
];

$loader->load($secrets);

echo DATABASE_NAME; // mydevdatabase
echo DATABASE_USER; // mydevdatabaseuser
```

For cases where you don't have separate credentials for each stage, skip the `setStage` method call.

```yaml
name: mydatabase
user: mydatabaseuser
password: verylongandsecretstring
```

```php
<?php

use TomodomoCo\SecretKeeper;

$loader = new SecretKeeper('/absolute/path/to/secrets/');

$secrets = [
    [
        'file'   => 'database',
        'type'   => 'yaml',
        'prefix' => 'db',
    ],
];

$loader->load($secrets);

echo DB_NAME; // mydatabase
echo DB_USER; // mydatabaseuser
echo DB_PASSWORD; // verylongandsecretstring
```

## About Tomodomo

Tomodomo is a creative agency for magazine publishers. We use custom design and technology to speed up your editorial workflow, engage your readers, and build sustainable subscription revenue for your business.

Learn more at [tomodomo.co](https://tomodomo.co) or email us: [hello@tomodomo.co](mailto:hello@tomodomo.co)

## License & Conduct

This project is licensed under the terms of the MIT License, included in `LICENSE.md`.

All open source Tomodomo projects follow a strict code of conduct, included in `CODEOFCONDUCT.md`. We ask that all contributors adhere to the standards and guidelines in that document.

Thank you!

# secret-keeper

Secret Keeper helps you load up various credential/secret files, typically YAML or JSON.

(Only YAML is supported at the moment.)

## Usage

Given the following YAML file, `database.yml`:

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

use Tomodomo\SecretKeeper;

$loader = new SecretKeeper('/absolute/path/to/secrets/', 'dev');

$secrets = [
	[
		'filename'  => 'database',
		'extension' => 'yml',
	],
];

$loader->load($secrets);

echo DATABASE_NAME; // mydevdatabase
echo DATABASE_USER; // mydevdatabaseuser
echo DATABASE_PASSWORD; // verylongandsecretstring
```

If you prefer, you can omit the stages in your files and in the constructor. You can also provide a custom prefix for your constants.

```yaml
name: mydatabase
user: mydatabaseuser
password: verylongandsecretstring
```

```php
<?php

use Tomodomo\SecretKeeper;

$loader = new SecretKeeper('/absolute/path/to/secrets/');

$secrets = [
	[
		'filename'  => 'database',
		'extension' => 'yml',
		'prefix'    => 'db',
	],
];

$loader->load($secrets);

echo DB_NAME; // mydatabase
echo DB_USER; // mydatabaseuser
echo DB_PASSWORD; // verylongandsecretstring
```

## About Tomodomo

Tomodomo is a creative agency for communities. We focus on unique design and technical solutions to grow community activity and increase customer retention for online networking forums and customer service communities.

Learn more at [tomodomo.co](https://tomodomo.co) or email us: [hello@tomodomo.co](mailto:hello@tomodomo.co)

## License & Conduct

This project is licensed under the terms of the MIT License, included in `LICENSE.md`.

All open source Tomodomo projects follow a strict code of conduct, included in `CODEOFCONDUCT.md`. We ask that all contributors adhere to the standards and guidelines in that document.

Thank you!

# Laravel-Modules

| **Laravel** | **laravel-modules** | **southapi-module** |
|-------------|---------------------|----------------------|
| 10.0        | ^10.0               | ^1.0.0               |


`hanseaticdev/southapi-module` Laravel module with support methods like logging, masking etc

## Install

To use this requires lavavel-modules by nwidart. Please check the according information.

To install through Composer, by run the following command:

``` bash
composer require hanseaticdev/southapi-module
```

The package will automatically register a service provider and alias.
Make sure so add this lines to the config/modules.php of your laval-application

``` bash
    'scan' => [
        'enabled' => true,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
```

Enable the module by running:

``` bash
php artisan module:enable SouthAPI
```

### Autoloading

By default, the module classes are not loaded automatically. You can autoload your modules using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "Modules/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
  }

}
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**

## Documentation

will follow soon

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# Action Log

Laravel library for logging desired column changes in models

## Installation

Use the package manager [composer](https://getcomposer.org/download/) to install Action log.

```bash
composer required asd-lt/action-log
```

Copy migration

```bash
php artisan action-log:tables
```

Run migrations

```bash
php artisan migrate
```

## Configuration & Usage

Default guard will be used to link user with log, or it can be defined in `auth.php` configuration file.

```php
'defaults' => [
    // ...
    'action_log_guard' => 'guard',
],
```

Attach action log trait to any model, which changes should be logged.

```php
use Asd\ActionLog\Models\Traits\ActionLogTrait;

class Model {
    use ActionLogTrait;
    // ...
}
```

By default, all columns defined in `fillable` model attribute will be logged, but if needed it can be limited with additional model attribute `loggableFields`
```php
class Model {
    // log only these column changes
    protected $loggableFields = [
        'column1',
        'column2',
    ];
}
```

Additionally, columns can be excluded from logging.
```php
class Model {
    // exclude columns from logging changes
    protected $actionLogAttributesExcept = [
        'column3',
        'column4',
    ];
}
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
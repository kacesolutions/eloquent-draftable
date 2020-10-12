# Eloquent Draftable

- [Introduction](#introduction)
- [Installation](#installation)

## Introduction

Eloquent Draftable provides additional draftable features for your model elements. This package gives you the feature to publish a model or to publish on a scheduled basis or to publish on a certain date.

## Installation 

You may install Eloquent Draftable via Composer:
```bash 
composer require kace/eloquent-draftable
```

Next, add the nullable timestamp column `publisheh_at` to the model table database:
```php
$table->timestamp('published_at')->nullable();
```

Finally, you should run your database migrations. Eloquent Draftable will add a `published_at` column in which to publication date:
```bash
php artisan migrate
```

## License
This package is licensed under the MIT license.

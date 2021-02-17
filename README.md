# DRY Blog
Blog package for dry including migrations, backend managers, i18n support, categories, blocks, photos and authors. 

#### Index

* [Installation](#installation)
* [Usage](#usage)

#### Installation
```ssh
composer require dietervyncke/dry-blog

php oak migration migrate -m blog
```

##### Config options
Name					                          | Default
------------------- | ---------------------------------------------------------
categories          | true
authors             | true
photos              | true
advanced-layout     | true
types               | ['text-photo', 'photo-text', 'text', 'text-frame', 'quote', 'quote-text', 'text-quote', 'text-video', 'video-text']
languages           | ['nl', 'en', 'fr']
private             | false
required-fields     | ['photo', 'title', 'slug', 'publication_date']

#### Usage

##### Register the service provider
```php
<?php

$app = new \Oak\Application();

$app->register([
    \Tnt\Blog\BlogServiceProvider::class,
]);

$app->bootstrap();
```
##### Implementation example
 
###### Controller
```php
<?php

class blog extends base
{
    public static function index(Request $request, Page $page)
    {
        $app = Application::get();

        $dataList = $app->getWith(DataListInterface::class, [
            'repository' => BlogPosts::published()->recent(),
            'urlBuilder' => $app->getWith(BuilderInterface::class, [
                'base' => \dry\url('pages::view', $page),
            ]),
        ]);
        
        $tpl = parent::get_base_template($request, $page);
        $tpl->blogPosts = $dataList->getResults();
        $tpl->render( 'blog/index.tpl' );
    }
}
```

# EdgarEzWebPushBundle

## Installation

### Get the bundle using composer

Add EdgarEzWebPushBundle by running this command from the terminal at the root of
your symfony project:

```bash
composer require edgar/ez-webpush-bundle
```

## Enable the bundle

To start using the bundle, register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Edgar\EzWebPushBundle\EdgarEzWebPushBundle(),
        // ...
    );
}
```

## Add doctrine ORM support

in your ezplatform.yml, add

```yaml
doctrine:
    orm:
        auto_mapping: true
```

## Update your SQL schema

```
php bin/console doctrine:schema:update --force
```

## Add routing

Add to your global configuration app/config/routing.yml

```yaml
edgar.ezwebpush:
    resource: '@EdgarEzWebPushBundle/Resources/config/routing.yml'
    defaults:
        siteaccess_group_whitelist: 'admin_group'

edgar.ezwebpush.sw:
    resource: '@EdgarEzWebPushBundle/Resources/config/routing_sw.yml'    
```

## Add config

in app/config.yml, add :

```yaml
edgar_ez_web_push:
    subject: '%edgar_ez_web_push.subject%' # Your host
    vapid_public_key: '%edgar_ez_web_push.vapid_public_key%' # A VAPID public key
    vapid_private_key: '%edgar_ez_web_push.vapid_private_key%' # A VAPID private key
```

## Generate VAPID keys

Use this command and report keys to your config

```
php bin/console edgar:webpush:vapidkeys
```

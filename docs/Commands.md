Commands
========

The available commands are:

- `shq:users:create <unique> <pass> [--provider]`

## `shq:users:create`

Use this command to create a new user in the database.

### Overwriting `shq:users:create`

The command `shq:users:create` has a `protected` method `create()`.

Create your command `app:users:create` that extends `SerendipityHQ\Bundle\UsersBundle\Command\UsersCreateCommand`.

Then overwrite the `create()` method to do what you like with the just created user:

```php
...
        /**
         * {@inheritDoc}
         */
        protected function create(string $provider, string $unique, string $pass):UserInterface
        {
            $user = parent::create($provider, $unique, $pass);

            // Do what you like with the $user
            // $user->setYourProperty($yourValue);

            return $user;
        }
```

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

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

<hr />
<h3 align="center">
    <b>Do you like this bundle?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />

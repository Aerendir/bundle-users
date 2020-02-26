# SHQUsersBundle

A bundle that helps managing users in Symfony apps.

# Documentation

- Bootstrapping users management in your app
- The `UsersManager`
- Events
- Commands

## Configuring the `userInterface` entity

Implemnt `HasPlainPassword` and `use HasPlainPasswordTrait`.

This is will activate the Doctrine listener that will automatically encode the passwords properly.

## Creating a User with the manager

Method `create()` dispatches an event `UserCreatedEvent`.

You can listen for it and, if you like, you can set `UserCreatedEvent::stopPropagation()`.

If `true === UserCreatedEvent::isPropagationStopped()`, then the menager will not persist the user in the database, but will anyway return it.

This way you can decide what to do: create a new user, persist it by yourself, etc.

The manager will never call `EntityManager::flush()`: it is always your responsibility to call it and decide if and when to do so.

The method `UserInterface::eraseCredentials()` is never called by the bundle: it is a responsibility of yours as we don't know if you need them one more time (for example, to send an email to the registered user with the plain passowrd).

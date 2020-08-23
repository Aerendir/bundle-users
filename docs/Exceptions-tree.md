## Exceptions tree

This is the tree representing the Exceptions thron by Serendipity HQ Users Bundle:

- `UsersException`
    - `UserClassMustImplementInterface`
        - `UserClassMustImplementHasPlainPasswordInterface`
        - `UserClassMustImplementHasRolesInterface`
        - `UserClassMustImplementUserInterface`
    - `PasswordException`
        - `PasswordEncodingError`
        - `PasswordRequired`
        - `PasswordResetException`
            - `PasswordResetRequestException`
                - `PasswordResetTokenTooMuchFastRequests`
                - `PasswordResetTokenTooMuchStillActive`
            - `PasswordResetTokenException`
                - `PasswordResetTokenExpired`
                - `PasswordResetTokenInvalid`
    - `RolesException`
        - `RoleInvalidException`

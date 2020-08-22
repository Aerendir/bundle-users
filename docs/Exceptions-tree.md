## Exceptions tree

This is the tree representing the Exceptions thron by Serendipity HQ Users Bundle:

- `UsersException`
    - `UserClassMustImplementUserInterface`
    - `UserClassMustImplementHasPlainPasswordInterface`
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

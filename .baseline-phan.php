<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanDeprecatedFunction : 20+ occurrences
    // PhanAccessMethodInternal : 9 occurrences
    // PhanUnreferencedClass : 8 occurrences
    // PhanAccessClassConstantInternal : 2 occurrences
    // PhanUndeclaredMethod : 1 occurrence
    // PhanUnusedPublicFinalMethodParameter : 1 occurrence
    // PhanWriteOnlyPrivateProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/AbstractUserActivationCommand.php' => ['PhanDeprecatedFunction'],
        'src/Command/AbstractUserCommand.php' => ['PhanDeprecatedFunction'],
        'src/Command/AbstractUserRolesCommand.php' => ['PhanDeprecatedFunction'],
        'src/Command/AbstractUsersCommand.php' => ['PhanDeprecatedFunction'],
        'src/Command/RoleAddCommand.php' => ['PhanDeprecatedFunction', 'PhanUnreferencedClass'],
        'src/Command/RoleRemCommand.php' => ['PhanDeprecatedFunction', 'PhanUnreferencedClass'],
        'src/Command/UserActivateCommand.php' => ['PhanDeprecatedFunction', 'PhanUnreferencedClass'],
        'src/Command/UserCreateCommand.php' => ['PhanDeprecatedFunction', 'PhanUndeclaredMethod', 'PhanUnreferencedClass'],
        'src/Command/UserDeactivateCommand.php' => ['PhanDeprecatedFunction', 'PhanUnreferencedClass'],
        'src/DependencyInjection/SHQUsersExtension.php' => ['PhanUnreferencedClass'],
        'src/Exception/PasswordResetTokenClassMustImplementPasswordResetTokenInterfaceInterface.php' => ['PhanDeprecatedFunction'],
        'src/Exception/PasswordResetTokenClassNotImplemented.php' => ['PhanDeprecatedFunction'],
        'src/Exception/UserClassMustImplementInterface.php' => ['PhanDeprecatedFunction'],
        'src/Exception/UsersManagerException.php' => ['PhanDeprecatedFunction'],
        'src/Form/Type/UserPasswordChangeType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Helper/PasswordResetHelper.php' => ['PhanAccessMethodInternal', 'PhanDeprecatedFunction'],
        'src/Manager/PasswordManager.php' => ['PhanAccessMethodInternal'],
        'src/Model/Property/HasPlainPasswordTrait.php' => ['PhanUnreferencedClass'],
        'src/Model/Property/PasswordResetTokenTrait.php' => ['PhanUnreferencedClass', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/ResetPasswordTokenComponents.php' => ['PhanDeprecatedFunction'],
        'src/Repository/PasswordResetTokenRepository.php' => ['PhanDeprecatedFunction'],
        'src/Util/PasswordResetTokenGenerator.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

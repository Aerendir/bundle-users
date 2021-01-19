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
    // PhanRedefinedClassReference : 30+ occurrences
    // PhanAccessMethodInternal : 9 occurrences
    // PhanUnreferencedClass : 3 occurrences
    // PhanAccessClassConstantInternal : 2 occurrences
    // PhanRedefinedExtendedClass : 1 occurrence
    // PhanUndeclaredMethod : 1 occurrence
    // PhanUnusedPublicFinalMethodParameter : 1 occurrence
    // PhanWriteOnlyPrivateProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/AbstractUserActivationCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/AbstractUserRolesCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/AbstractUsersCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/RoleAddCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/RoleRemCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/UserActivateCommand.php' => ['PhanRedefinedClassReference'],
        'src/Command/UserCreateCommand.php' => ['PhanRedefinedClassReference', 'PhanUndeclaredMethod'],
        'src/Command/UserDeactivateCommand.php' => ['PhanRedefinedClassReference'],
        'src/DependencyInjection/SHQUsersExtension.php' => ['PhanRedefinedClassReference', 'PhanUnreferencedClass'],
        'src/Form/Type/UserPasswordChangeType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Helper/PasswordResetHelper.php' => ['PhanAccessMethodInternal'],
        'src/Manager/PasswordManager.php' => ['PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
        'src/Manager/UsersManager.php' => ['PhanRedefinedClassReference'],
        'src/Manager/UsersManagerInterface.php' => ['PhanRedefinedClassReference'],
        'src/Model/Property/HasPlainPasswordTrait.php' => ['PhanUnreferencedClass'],
        'src/Model/Property/PasswordResetTokenTrait.php' => ['PhanUnreferencedClass', 'PhanWriteOnlyPrivateProperty'],
        'src/Repository/PasswordResetTokenRepository.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass'],
        'src/Util/PasswordResetTokenGenerator.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

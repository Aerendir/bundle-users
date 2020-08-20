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
    // PhanUnreferencedPublicMethod : 25+ occurrences
    // PhanRedefinedClassReference : 15+ occurrences
    // PhanAccessMethodInternal : 9 occurrences
    // PhanUndeclaredMethod : 5 occurrences
    // PhanUnreferencedClass : 3 occurrences
    // PhanUnusedPublicFinalMethodParameter : 3 occurrences
    // PhanAccessClassConstantInternal : 2 occurrences
    // PhanTypeMismatchArgumentNullable : 2 occurrences
    // PhanWriteOnlyPrivateProperty : 2 occurrences
    // PhanReadOnlyPublicProperty : 1 occurrence
    // PhanRedefinedExtendedClass : 1 occurrence
    // PhanTypeMismatchArgument : 1 occurrence
    // PhanTypeMismatchDeclaredParam : 1 occurrence
    // PhanUndeclaredClassReference : 1 occurrence
    // PhanUnreferencedProtectedProperty : 1 occurrence
    // UndeclaredTypeInInlineVar : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/UsersCreateCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUnreferencedProtectedProperty', 'UndeclaredTypeInInlineVar'],
        'src/DependencyInjection/SHQUsersExtension.php' => ['PhanUnreferencedClass'],
        'src/Doctrine/UserEncodePasswordListener.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentNullable', 'PhanUndeclaredMethod', 'PhanUnreferencedPublicMethod'],
        'src/Event/PasswordResetTokenCreatedEvent.php' => ['PhanUnreferencedPublicMethod'],
        'src/Event/PasswordResetTokenCreationFailedEvent.php' => ['PhanUnreferencedPublicMethod'],
        'src/Event/UserCreatedEvent.php' => ['PhanTypeMismatchDeclaredParam', 'PhanUnreferencedPublicMethod'],
        'src/Form/Type/ChangePasswordFormType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Form/Type/PasswordResetRequestType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Form/Type/UserPasswordChangeType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Helper/PasswordHelper.php' => ['PhanUnreferencedPublicMethod'],
        'src/Helper/PasswordResetHelper.php' => ['PhanAccessMethodInternal', 'PhanUnreferencedPublicMethod', 'PhanWriteOnlyPrivateProperty'],
        'src/Manager/Exception/UsersManagerException.php' => ['PhanUndeclaredClassReference'],
        'src/Manager/PasswordManager.php' => ['PhanAccessMethodInternal', 'PhanReadOnlyPublicProperty', 'PhanUnreferencedPublicMethod'],
        'src/Manager/UsersManagerRegistry.php' => ['PhanUnreferencedPublicMethod'],
        'src/Model/PasswordResetTokenPublic.php' => ['PhanUnreferencedPublicMethod'],
        'src/Model/Property/HasPlainPasswordTrait.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/Model/Property/PasswordResetTokenTrait.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod', 'PhanWriteOnlyPrivateProperty'],
        'src/Util/PasswordResetTokenGenerator.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal', 'PhanRedefinedClassReference'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

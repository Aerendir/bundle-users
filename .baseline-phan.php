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
    // PhanAccessMethodInternal : 9 occurrences
    // PhanUndeclaredMethod : 3 occurrences
    // PhanUnreferencedClass : 3 occurrences
    // PhanAccessClassConstantInternal : 2 occurrences
    // PhanUndeclaredClassMethod : 1 occurrence
    // PhanUndeclaredTypeParameter : 1 occurrence
    // PhanUndeclaredTypeProperty : 1 occurrence
    // PhanUnusedPublicFinalMethodParameter : 1 occurrence
    // PhanWriteOnlyPrivateProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/UserCreateCommand.php' => ['PhanUndeclaredMethod'],
        'src/DependencyInjection/SHQUsersExtension.php' => ['PhanUnreferencedClass'],
        'src/Form/Type/UserPasswordChangeType.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Helper/PasswordHelper.php' => ['PhanUndeclaredClassMethod', 'PhanUndeclaredMethod', 'PhanUndeclaredTypeParameter', 'PhanUndeclaredTypeProperty'],
        'src/Helper/PasswordResetHelper.php' => ['PhanAccessMethodInternal'],
        'src/Manager/PasswordManager.php' => ['PhanAccessMethodInternal'],
        'src/Model/Property/HasPlainPasswordTrait.php' => ['PhanUnreferencedClass'],
        'src/Model/Property/PasswordResetTokenTrait.php' => ['PhanUnreferencedClass', 'PhanWriteOnlyPrivateProperty'],
        'src/Util/PasswordResetTokenGenerator.php' => ['PhanAccessClassConstantInternal', 'PhanAccessMethodInternal'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

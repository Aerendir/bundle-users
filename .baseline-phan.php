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
    // PhanRedefinedClassReference : 15+ occurrences
    // PhanUnreferencedPublicMethod : 6 occurrences
    // PhanUndeclaredMethod : 5 occurrences
    // PhanUnreferencedClass : 3 occurrences
    // PhanRedefinedExtendedClass : 2 occurrences
    // PhanTypeMismatchArgumentNullable : 2 occurrences
    // PhanCommentParamWithoutRealParam : 1 occurrence
    // PhanTypeMismatchArgument : 1 occurrence
    // PhanTypeMismatchDeclaredParam : 1 occurrence
    // PhanUnreferencedProtectedProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Command/UsersCreateCommand.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUnreferencedProtectedProperty'],
        'src/DependencyInjection/SHQUsersExtension.php' => ['PhanUnreferencedClass'],
        'src/Doctrine/UserEncodePasswordListener.php' => ['PhanCommentParamWithoutRealParam', 'PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentNullable', 'PhanUndeclaredMethod', 'PhanUnreferencedPublicMethod'],
        'src/Event/UserCreatedEvent.php' => ['PhanRedefinedExtendedClass', 'PhanTypeMismatchDeclaredParam', 'PhanUnreferencedPublicMethod'],
        'src/Manager/UsersManagerRegistry.php' => ['PhanUnreferencedPublicMethod'],
        'src/Property/HasPlainPasswordTrait.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/SHQUsersBundle.php' => ['PhanUnreferencedClass'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

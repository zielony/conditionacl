# Conditionacl

Conditionacl is an implementation of a simple access-control list (ACL) system that also takes external conditions into account when validating the rules.

Example usage:
```
$aclFactory = new AclFactory(new YamlFileConfig('...'));
$acl = $aclFactory->fromRole('project-reader');

$user1 = new Domain\User(1, 'user1');
$user2 = new Domain\User(2, 'user2');

$project = new Domain\Project(1, 'first', $user1);

$acl->hasPermissionTo('read', 'project', new Condition\IfUserIsProjectsOwner($user1, $project));
// true

$acl->hasPermissionTo('read', 'project', new Condition\IfUserIsProjectsOwner($user2, $project));
// false
```

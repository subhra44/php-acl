<?php

require_once __DIR__ . '/Acl.php';

// Example 1.1 - create role
/* $role = array( 'name'  => "vp" );
$permissions = array( 'add_user', 'update_user', 'delete_user', 'add_role', 'update_role', 'delete_role', 'update_profile' );
$acl = new Acl();
$role_id = $acl->createRole($role, $permissions); */

// Example 1.2 - create role
/* $role = array( 'name'  => "tech_manager" );
$permissions = array( 'add_user', 'update_user', 'delete_user' );
$acl = new Acl();
$role_id = $acl->createRole($role, $permissions); */

// Example 1.3 - create role
/* $role = array( 'name'  => "developer" );
$permissions = array( 'update_profile' );
$acl = new Acl();
$role_id = $acl->createRole($role, $permissions); */

// Example 2.1 - create user
/* $user = array( 'name'  => "A" );
$roles = array( 'vp' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 2.2 - create user
/* $user = array( 'name'  => "F" );
$roles = array( 'vp' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 2.3 - create user
/* $user = array( 'name'  => "B" );
$roles = array( 'tech_manager' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 2.4 - create user
/* $user = array( 'name'  => "C" );
$roles = array( 'tech_manager' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 2.5 - create user
/* $user = array( 'name'  => "D" );
$roles = array( 'developer' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 2.6 - create user
/* $user = array( 'name'  => "E" );
$roles = array( 'developer' );
$acl = new Acl();
$user_id = $acl->createUser($user, $roles); */

// Example 3.1 - assign users as reportees
/* $user_id = 3;
$reportee_ids = array( 1 );
$acl = new Acl();
$acl->assignUser($user_id, $reportee_ids); */

// Example 3.2 - assign users as reportees
/* $user_id = 5;
$reportee_ids = array( 3, 4 );
$acl = new Acl();
$acl->assignUser($user_id, $reportee_ids); */

// Example 3.3 - assign users as reportees
/* $user_id = 4;
$reportee_ids = array( 1 );
$acl = new Acl();
$acl->assignUser($user_id, $reportee_ids); */

// Example 4 - list of all users who report to the current user
/* $user_id = 1;
$acl = new Acl();
$users = $acl->getUser($user_id);
print_r($users); */

// Example 5 - list of all users who the current user reports to
/* $user_id = 5;
$acl = new Acl();
$reportees = $acl->getReportees($user_id);
print_r($reportees); */

// Example 6 - list of privileges of the current user
/* $user_id = 5;
$acl = new Acl();
$permissions = $acl->getPermissions($user_id);
print_r($permissions); */
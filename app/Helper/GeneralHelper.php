<?php

use App\Models\Permission;

function getParentShowOf($parent)
{
    $routeName = str_replace('admin.', '', $parent);
    $permission = Permission::where('as', $routeName)->first();
    return $permission ? $permission->parent_show : $routeName;
}

function getParentOf($parent)
{
    $routeName = str_replace('admin.', '', $parent);
    $permission = Permission::where('as', $routeName)->first();
    return $permission ? $permission->parent : $routeName;
}

function getParentIdOf($parent)
{
    $routeName = str_replace('admin.', '', $parent);
    $permission = Permission::where('as', $routeName)->first();
    return $permission ? $permission->id : $routeName;
}

function getIdMenuOf($parent)
{
    $permission = Permission::where('id', $parent)->first();
    return $permission ? $permission->parent_show : null;
}
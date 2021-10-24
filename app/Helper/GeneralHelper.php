<?php

use App\Models\Permission;
use Spatie\Valuestore\Valuestore;

function getSettingsOf($key)
{
    $settings = Valuestore::make(config_path('settings.json'));
    return $settings->get($key);
}


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
    return $permission ? $permission->parent : null;
}

function getParentIdOf($parent)
{
    $routeName = str_replace('admin.', '', $parent);
    $permission = Permission::where('as', $routeName)->first();
    return $permission ? $permission->id : null;
}

function getIdMenuOf($parent)
{
    $permission = Permission::where('id', $parent)->first();
    return $permission ? $permission->parent_show : null;
}
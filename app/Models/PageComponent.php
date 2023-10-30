<?php

namespace App\Models;

// use Illuminate\Contracts\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PageComponent extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';

    public function componentData(){
        // return $this->hasMany(PageComponentData::class, 'component_id', 'id');
        return $this->hasMany(PageComponentData::class, 'component_id', 'id');
    }
    public function parentMenu()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function menuTree()
    {
        return $this->with('items')->where('parent_id', 0)->orderBy('display_order')->groupBy('group')->get();
    }

    public function buildMenuTree(Collection $menus, $parentId = 0)
    {
        $menuTree = collect();

        foreach ($menus as $menu) {
            if ($menu->parent_id == $parentId) {
                $children = $this->buildMenuTree($menus, $menu->id);
                if ($children->isNotEmpty()) {
                    $menu->setAttribute('children', $children);
                }
                $menuTree->push($menu);
            }
        }

        return $menuTree;
    }

    public function menuTreeWithHierarchy()
    {
        $menus = $this->menuTree();
        $menuTree = $this->buildMenuTree($menus);

        return $menuTree;
    }
}

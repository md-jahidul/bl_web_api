<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page\NewPageComponent;
use Illuminate\Support\Collection;

class NewPageComponentData extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function NewPageComponent(){
        return $this->hasMany( NewPageComponentData::class, 'parent_id', 'id' );
    }
    public function data(){
        return $this->belongsTo(NewPageComponent::class)->withDefault();
    }
    // protected $table = 'page_component_data';
    public function parentMenu()
    {
        return $this->belongsTo(NewPageComponentData::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(NewPageComponentData::class, 'parent_id');
    }

    public function menuTree($component_id)
    {
        return $this->with('items')->where('parent_id', 0)->where('component_id', $component_id)->orderBy('id')->get();
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

    public function menuTreeWithHierarchy($component_id = 0)
    {
        $menus = $this->menuTree($component_id);
        $menuTree = $this->buildMenuTree($menus);

        return $menuTree;
    }
}

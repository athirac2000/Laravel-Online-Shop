<?php

namespace App\Models;

use App\Models\admin\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

//     In Laravel, when you create a model, Laravel assumes a default table name based on the pluralized version of the model name. In your case, you have a Category model, and Laravel assumes that the corresponding table name is "categories." This naming convention follows the Laravel convention over configuration principle.

// So, even though you don't explicitly specify the table name in your Category model, Laravel automatically associates the model with the "categories" table. This convention allows you to quickly develop without the need for extensive configuration, as long as you follow the naming conventions.

    public function sub_category(){
        return $this->hasMany(SubCategory::class);
    }

}



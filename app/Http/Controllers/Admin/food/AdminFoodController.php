<?php

namespace App\Http\Controllers\Admin\food;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Restaurant;

class AdminFoodController extends Controller
{

    public function restaurant($id)
    {
        $foods = Food::where('restaurant_id',$id)->paginate();
        $rest = Restaurant::find($id);
        return view('admin.food.restaurant',compact(['foods', 'rest']));

    }

}

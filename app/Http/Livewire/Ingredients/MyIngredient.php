<?php

namespace App\Http\Livewire\Ingredients;

use Livewire\Component;
use App\Models\IngredientsUser;
use Illuminate\Support\Facades\Auth;
use DB;
use Livewire\WithPagination;

class MyIngredient extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $sortField = 'ingredients.name';
    public $sortAsc = true;
    public $selected = [];
    public $selectedAll = false;
    public $selectedIngNames;

    public function updatedSelectedAll($value){
        if($value){
            $this->selected=DB::table("ingredients_users")
            ->where('user_id',Auth::user()->id)
            ->join('ingredients', 'ingredients.id', '=', 'ingredients_users.ingredient_id')
            ->pluck('ingredients.name')
            ->toArray();
            return $this->selected;
        }
        else{
            $this->selected=[];
        }
    }
    public function searchRecipes(){
        // dd($this->selected);

        foreach($this->selected as $string) {
            $this->selectedIngNames .= $string.",";
        }
        $this->emit('emitShowMyIngdientRecipes',$this->selectedIngNames);
        
    }
    
    public function showIngredientInfo($ingredientId){
		$this->emit('emitshowMyIngdientInfo',$ingredientId);                                
	}
    public function deleteIngredient($ingredientId){
		$ingredient = DB::table("ingredients_users")
                                ->where('user_id',Auth::user()->id)
                                ->where('ingredient_id',$ingredientId)
                                ->delete();
       
                                $this->dispatchBrowserEvent('swal:modal',[
            'type' => 'success',
            'title' => 'Ingredient deleted successfully',
            'text' => '',
        ]);
                                
	}
    
    public function render(){
        return view('livewire.ingredients.my-ingredient', [
            'myIngredients' => IngredientsUser::search($this->search)
                ->where('user_id',Auth::user()->id)
                ->join('ingredients', 'ingredients.id', '=', 'ingredients_users.ingredient_id')
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->simplePaginate($this->perPage),
                
        ]);
    }

}     
               

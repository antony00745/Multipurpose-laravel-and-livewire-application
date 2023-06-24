<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class ListUsers extends Component
{ 
    public $state=[];
    public $user;
    public $userIdBeingRemoved=null;
    public $showEditModal=false;
    public function render()
    {
        $users=User::latest()->paginate();
        return view('livewire.admin.users.list-users',
    [
        'users' => $users,
    ]
    );
    }
    public function addnew()
    {
        $this->state=[];
        $this->showEditModal=false;
        $this->dispatchBrowserEvent('show-form');
    }
    public function edit(user $user)
    {
        $this->user=$user;
        $this->showEditModal=true;
        $this->state= $user->toArray();
        $this->dispatchBrowserEvent('show-form');
    }
    public function confirmUserRemoval($userId)
    {
        $this->userIdBeingRemoved=$userId;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function createUser()
    {
        
        $validatedData=Validator::make($this->state,[
            'name' => 'required',
            'email'=> 'required|email|unique:users',
            'password' => 'required|confirmed'
        ])->validate();
        $validatedData['password']=bcrypt($validatedData['password']);
        User::create($validatedData);
       // session()->flash('message','user added successfully!');
        $this->dispatchBrowserEvent('hide-form',['message'=>'user added successfully!']);
        return redirect()->back();
       
    }
    public function updateUser()
    {
        
        $validatedData=Validator::make($this->state,[
            'name' => 'required',
            'email'=> 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|confirmed'
        ])->validate();
        if(!empty($validatedData['password']))
        {
        $validatedData['password']=bcrypt($validatedData['password']);
        }
        $this->user->update($validatedData);
       // session()->flash('message','user added successfully!');
        $this->dispatchBrowserEvent('hide-form',['message'=>'user updated successfully!']);
        return redirect()->back();
       
    }
    public function deleteUser(){
        $user=User::findOrFail($this->userIdBeingRemoved);
        $user->delete();
        $this->dispatchBrowserEvent('hide-delete-modal',['message'=>'user deleted successfully!']);
    }   
    
}

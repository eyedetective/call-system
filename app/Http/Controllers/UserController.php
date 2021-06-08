<?php

namespace App\Http\Controllers;

use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $permissions = ['Admin','Supervisor','Agent'];
    protected $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::when($request->search,function($q) use ($request){
            return $q->where('name','LIKE','%'.$request->search.'%')
                ->orWhere('username','LIKE','%'.$request->search.'%')
                ->orWhere('email','LIKE','%'.$request->search.'%');
        })
        ->when($request->inactive,function($q){
            return $q->onlyTrashed();
        })
        ->with('createdBy','updatedBy')
        ->paginate();
        $params = array('search'=>$request->search,'inactive'=>$request->inactive);
        return view('user.index',compact('users','params'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.add',['permissions'=>$this->permissions,'departments'=>Topic::all(),'days'=>$this->days]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->fill($request->except(['password']));
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return redirect()->to(route('user.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit',['user'=>$user,'permissions'=>$this->permissions,'departments'=>Topic::all(),'days'=>$this->days]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->fill($request->except(['password','oldpassword','newpassword','confirmpassword']));
        if($request->has('password') && $request->input('password')) $user->password = Hash::make($request->input('password'));
        $user->save();
        return redirect()->to(route('user.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->to(route('user.index'));
    }

    public function restore($id)
    {
        User::withTrashed()->where('id', $id)->restore();
        return redirect()->to(route('user.index'));
    }
}

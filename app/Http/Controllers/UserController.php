<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $permissions = ['Admin','Supervisor','Agent'];
    protected $departments = ['IT','Programmer'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::with('createdBy','updatedBy')->paginate();
        return view('user.index',['data'=>$data,'permissions'=>$this->permissions,'departments'=>$this->departments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.add',['permissions'=>$this->permissions,'departments'=>$this->departments]);
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
        return view('user.edit',['user'=>$user,'permissions'=>$this->permissions,'departments'=>$this->departments]);
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
        return $user;
    }

    public function restore($id)
    {
        User::withTrashed()->where('id', $id)->restore();
        return User::find($id);
    }
}

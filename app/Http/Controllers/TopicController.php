<?php

namespace App\Http\Controllers;

use App\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $topics = Topic::when($request->search,function($q) use ($request){
            return $q->where('name','LIKE','%'.$request->search.'%');
        })
        ->when($request->inactive,function($q){
            return $q->onlyTrashed();
        })
        ->with('createdBy','updatedBy')->paginate();
        $params = array('search'=>$request->search,'inactive'=>$request->inactive);
        return view('topic.index',compact('topics','params'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('topic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $topic = new Topic();
        $topic->fill($request->all());
        $topic->save();
        return redirect(route('topic.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic)
    {
        return $topic;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function edit(Topic $topic)
    {
        return view('topic.edit',['topic'=>$topic]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->save();
        return redirect(route('topic.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();
        return redirect(route('topic.index'));
    }

    public function restore($id)
    {
        Topic::withTrashed()->where('id', $id)->restore();
        return redirect(route('topic.index'));
    }
}

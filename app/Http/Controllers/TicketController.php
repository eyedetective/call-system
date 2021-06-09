<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->merge(['page' => ceil($request->input('start')/$request->input('length'))+1]);
        $tickets = Ticket::orderByDesc('id')
            ->when($request->has('search') && $request->input('search')['value'], function($q) use ($request){
                return $q->where('customer_name','like','%'.$request->input('search')['value'].'%')
                    ->orWhere('customer_phone','like','%'.$request->input('search')['value'].'%');
            })
            ->when(auth()->user()->permission == 'Agent', function($q){
                return $q->where('rep_user_id',auth()->user()->id)
                ->orWhere('assign_user_id',auth()->user()->id);
            })
            ->when($request->call_status, function($q) use ($request){
                return $q->where('call_status',$request->call_status);
            })
            ->when($request->status, function($q) use ($request){
                return $q->where('status',$request->status);
            })
            ->when($request->assignment, function($q) use ($request){
                return $q->where('assign_user_id',$request->assignment);
            })
            ->when($request->repBy, function($q) use ($request){
                return $q->where('rep_user_id',$request->repBy);
            })
            ->when($request->topic_id, function($q) use ($request){
                return $q->where('topic_id',$request->topic_id);
            })
            ->when($request->date_start && $request->date_end, function($q) use ($request){
                return $q->whereBetween('created_at',[$request->date_start.' 00:00:00',$request->date_end.' 23:59:59']);
            })
            ->with('repBy','assignTo','topic')
            ->paginate($request->input('length'));
        return response()->json(['recordsTotal'=> $tickets->total(), 'recordsFiltered'=> $tickets->total(),'data'=>$tickets->items()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ticket = new Ticket();
        $ticket->fill($request->all())->save();
        return response()->json($ticket);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        $ticket->rep_by = $ticket->load('repBy');
        $ticket->topic = $ticket->load('topic');
        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        $ticket->fill($request->all())->save();
        return response()->json($ticket);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json($ticket);
    }
}

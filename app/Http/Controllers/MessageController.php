<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $ids = Message::orderBy('id', 'DESC')->get(["id", "from_id", "to_id"]);

        $chatuserIds = [];

        foreach ($ids as $temp) {
            if ($temp['from_id'] != auth()->user()->id)
                $chatuserIds[] = $temp['from_id'];
            elseif ($temp['to_id'] != auth()->user()->id) {
                $chatuserIds[] = $temp['to_id'];
            }
        }

        $chatuserIds = array_unique($chatuserIds);
        $chatuserIds = array_values($chatuserIds);

        $chats = [];
        $k = 0;
        foreach ($chatuserIds as $userid) {
            $chatUserId = $userid;

            $chats[$k] = Message::where(function ($q) use ($chatUserId) {
                $q->where('from_id', $chatUserId)->orWhere('to_id', $chatUserId);
            })->select('message', 'created_at')->orderBy('id', 'DESC')->limit(1)->first();

            $chats[$k]['chat_with_user_id'] = $chatUserId;
            $user = User::find($chatUserId);
            $chats[$k]['name'] = $user ? $user->name : null;
            $chats[$k]['email'] = $user ? $user->email : null;
            $k++;
        }


        if ($request->has('isAjax')) {
            $messages = DB::table('messages')
                ->join('users', 'messages.from_id', '=', 'users.id')
                ->where('from_id', auth()->user()->id)
                ->orWhere('to_id', $request->toUserId)
                ->orderByDesc('messages.created_at')
                ->take(5)
                ->get()
                ->reverse();

            echo json_encode(['messages' => $messages, 'loggedInUserId' => auth()->user()->id]);
            exit;
        }

        $messages = DB::table('messages')
            ->join('users', 'messages.from_id', '=', 'users.id')
            // ->join('users', 'messages.to_id', '=', 'users.id')
            ->where('from_id', auth()->user()->id)
            ->orWhere('to_id', auth()->user()->id)
            ->orderByDesc('messages.created_at')
            ->take(5)
            ->get()
            ->reverse();

        return view('home', compact('messages', 'chats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message =  Message::create([
            'from_id' => auth()->user()->id,
            'message' => $request->input('message')
        ]);

        broadcast(new MessageSent($message->load('to_user')));
        echo json_encode(['msg' => 'Send successfully', 'time' => now()->format('H:i')]);
        exit;
        // return redirect()->back()->with(['message' => 'Message Sent']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}

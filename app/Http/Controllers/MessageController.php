<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function send_message(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'subject' => 'required|max:40',
            'content' => 'max:500'
        ]);
        if (isset($request->id)) $message = Message::find($request->id);
        else $message = new Message();
        $message->sender_id = $request->sender_id;
        $message->recipient_id = $request->recipient_id;
        $message->subject = $request->subject;
        $message->content = $request->content;
        if ($message->save()) $rs = SharedFunctions::success_msg("Message sent!");
        return response()->json($rs);
    }

    public function get(Request $request)
    {
        $messages = Message::leftJoin('users', 'messages.sender_id', '=', 'users.id')
            ->select('messages.*', 'users.email as sender_email')
            ->where('messages.recipient_id', Auth::id())
            ->get();
        // get user name with this query messages of 'sender_id'
        $rs = SharedFunctions::success_msg("");
        $rs['result'] = $messages;
        return response()->json($rs);
    }

    public function reply_message(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'subject' => 'required|max:40',
            'content' => 'max:500'
        ]);
        if (isset($request->id)) $message = Message::find($request->id);
        else $message = new Message();
        $message->sender_id = Auth::id();
        $message->recipient_id = $request->recipient_id;
        $message->subject = $request->subject;
        $message->content = $request->content;
        if ($message->save()) $rs = SharedFunctions::success_msg("Message sent!");
        return response()->json($rs);
    }

    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Message::find($request->id);
        if ($query->delete()) $rs = SharedFunctions::success_msg("Message deleted");
        return response()->json($rs);
    }
}

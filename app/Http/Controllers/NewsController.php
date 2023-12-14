<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\News;
use App\Models\User;
use App\Notifications\NewsCreatedNotification;
use App\Notifications\Notify;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = News::find($request->id);
        if ($query->forceDelete()) {
            $rs = SharedFunctions::success_msg("Event deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_NEWS, AuditTrail::ACTION_DELETE, "Event " . $query->question . " deleted"
            );
        }
        return response()->json($rs);
    }

    public function disable(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = News::find($request->id);
        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Event disabled");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_NEWS, AuditTrail::ACTION_DELETE, "Event " . $query->question . " disabled"
            );
        }
        return response()->json($rs);
    }

    public function get()
    {
        $news = News::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $news;
        return response()->json($rs);
    }

    public function get_active(Request $request)
    {
        $news = News::orderBy('created_at', 'DESC')
            ->limit($request->limit)
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $news;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'title' => 'required|max:120',
            'description' => 'required|max:255'
        ]);
        $new_news = false;
        if (isset($request->id)) $news = News::find($request->id);
        else { $news = new News(); $new_news = true; }
        $news->title = $request->title;
        $news->description = $request->description;
        if ($news->save()) {
            $this->news_created_notify($news->title);
            $rs = SharedFunctions::success_msg("Event saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_NEWS, $new_news ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Event " . $request->title . " saved"
            );
        }
        return response()->json($rs);
    }

    private function news_created_notify($news)
    {
        $users = User::where('user_type', User::TYPE_STUDENT)->get();
        Notify::send($users, new NewsCreatedNotification($news));
    }
}

<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\News;
use App\Models\User;
use App\Notifications\NewsCreatedNotification;
use App\Notifications\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

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
    public function enable(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        
        News::withTrashed()->find($request->id)->restore();

        $rs = SharedFunctions::success_msg('News enabled');
        return response()->json($rs);
    }
    public function get($sort_by)
    {
        $news = News::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        $query = News::withTrashed()
        ->orderBy('created_at', $sort_by == "created_at" ? 'DESC' : 'ASC')
            ->get()->map(function($q) {
                $q->image = $q->image !== null ? [$q->image] : [];
                return $q;
            });
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_active(Request $request)
    {
        $news = News::orderBy('created_at', 'desc')
            ->whereNull('deleted_at') 
            ->get()
            ->map(function ($q) {
                $q->image = $q->image !== null ? [$q->image] : [];
                return $q;
            });

        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $news;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $form = new Request(json_decode($request->form, true));
        if (isset($request->file) && $request->hasFile('file')) $form->merge(['image' => $request->file]);

        
        $validations = [
            'title' => 'required|max:120',
            'description' => 'required|max:255',
        ];
        if (isset($form->image) && !empty($form->image)) $validations['image'] = 'image|max:' . strval($form->filesize_limit / 1024);

        $this->validate($form, $validations, [
            'title.max:120' => "Please enter maximum of 120 characters",
            'description.max:120' => "Please enter maximum of 120 characters",
            'title.required' => "News title is required",
            'description.max:120' => "News description is required",
        ]);

        $new_news = false;
        if (isset($form->id)) $news = News::find($form->id);
        else { $news = new News(); $new_news = true; }
        
        $news->title = $form->title;
        $news->description = $form->description;
        if (isset($form->image) && !empty($form->image)) {
            $fext = 'jpg';
            $path = public_path('images/uploads/news/');
            if (File::exists($path . $news->image)) File::delete($path . $news->image);
            $filename = date('YmdwHis') . "_" . SharedFunctions::generate_random_string(7) . '_' . SharedFunctions::generate_random_string() . '.' . $fext;
            if (!File::isDirectory($path)) File::makeDirectory($path, 0755, true, true);
            $image = Image::make($form->image);
            $image->save($path . '/' . $filename, 60, $fext);
            $news->image = $filename;
        }
        
        if ($news->save()) {
            $this->news_created_notify($news->title);
            $rs = SharedFunctions::success_msg("Event saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_NEWS, $new_news ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Event " . $request->title . " saved"
            );
        }
        end: return response()->json($rs);
    }

    private function news_created_notify($news)
    {
        $users = User::where('user_type', User::TYPE_STUDENT)->get();
        Notify::send($users, new NewsCreatedNotification($news));
    }
}

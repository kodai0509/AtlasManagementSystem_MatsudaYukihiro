<?php

namespace App\Http\Controllers\Authenticated\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Gate;
use App\Models\Users\User;
use App\Models\Users\Subjects;
use App\Searchs\DisplayUsers;
use App\Searchs\SearchResultFactories;

class UsersController extends Controller
{

    public function showUsers(Request $request)
    {
        $keyword = $request->input('keyword');
        $category = $request->input('category', 'over_name');
        $updown = $request->input('updown', 'ASC');
        $gender = $request->input('sex');
        $role = $request->input('role');
        $subject_ids = $request->input('subjects');

        // 検索機能
        $query = User::with('subjects')
            ->where(function ($q) use ($keyword, $category) {
                if ($keyword) {
                    if ($category === 'id') {
                        $q->where('id', 'like', '%' . $keyword . '%');
                    } else {
                        $q->where('over_name', 'like', '%' . $keyword . '%')
                            ->orWhere('under_name', 'like', '%' . $keyword . '%')
                            ->orWhere('over_name_kana', 'like', '%' . $keyword . '%')
                            ->orWhere('under_name_kana', 'like', '%' . $keyword . '%');
                    }
                }
            });

        if ($gender) {
            $query->where('sex', $gender);
        }
        if ($role) {
            $query->where('role', $role);
        }
        if (is_array($subject_ids) && count($subject_ids) > 0) {
            $query->whereHas('subjects', function ($q) use ($subject_ids) {
                $q->whereIn('subjects.id', $subject_ids);
            });
        }
        $query->orderBy('over_name_kana', $updown ?? 'ASC');

        $users = $query->get();

        $subjects = Subjects::all();

        return view('authenticated.users.search', compact('users', 'subjects'));
    }


    public function userProfile($id)
    {
        $user = User::with('subjects')->findOrFail($id);
        $subject_lists = Subjects::all();
        return view('authenticated.users.profile', compact('user', 'subject_lists'));
    }
    public function userEdit(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->subjects()->sync($request->subjects);
        return redirect()->route('user.profile', ['id' => $request->user_id]);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $user = [
            'id' => 1,
            'name' => 'Hien Pham',
            'username' => 'hienpham',
            'avatar' => 'https://i.pravatar.cc/80?img=12'
        ];

        $posts = collect(range(1, 5))->map(function($i) {
            return [
                'id' => $i,
                'author' => [
                    'id' => $i,
                    'name' => 'User '.$i,
                    'username' => 'user'.$i,
                    'avatar' => "https://i.pravatar.cc/80?img=".($i+10),
                ],
                'content' => "Bài viết demo số $i.",
                'media' => $i % 2 == 0 ? "https://picsum.photos/seed/p$i/800/400" : null,
                'likes' => rand(1,50),
                'created_at' => now()->subMinutes($i*3)->diffForHumans(),
                'replies' => [
                    [
                        'id' => $i*10,
                        'author' => [
                            'id' => 100+$i,
                            'name' => 'Reply '.$i,
                            'username' => 'reply'.$i,
                            'avatar' => 'https://i.pravatar.cc/80?img='.($i+20),
                        ],
                        'content' => "Đây là reply demo cho post $i"
                    ]
                ]
            ];
        })->toArray();

        $trends = ['Laravel','PHP','Tailwind','AI'];
        $whoToFollow = collect(range(1,3))->map(function($i){
            return [
                'id'=>$i,
                'name'=>'Gợi ý '.$i,
                'username'=>'suggest'.$i,
                'avatar'=>'https://i.pravatar.cc/80?img='.(30+$i),
                'followed'=>false
            ];
        })->toArray();

        return view('home', compact('user','posts','trends','whoToFollow'));
    }

    public function post(Request $request) {
        return redirect()->route('threads.index');
    }

    public function like($id) {
        return redirect()->back();
    }

    public function reply(Request $request, $id) {
        return redirect()->back();
    }

    public function follow(Request $request, $id) {
        return redirect()->back();
    }

    public function loadMore(Request $request) {
        return redirect()->back();
    }
}

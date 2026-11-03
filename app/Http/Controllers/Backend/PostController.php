<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        $settings = Setting::whereIn('key', [
            'blog_title',
            'blog_subtitle',
            'blog_eyebrow',
            'blog_heading',
        ])->pluck('value', 'key')->toArray();

        return view('backend.posts.index', compact('posts', 'settings'));
    }

    public function create()
    {
        return view('backend.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'summary' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['author'] = $request->input('author', 'Admin');
        $data['published_at'] = $request->input('published_at', now());
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Post $post)
    {
        return view('backend.posts.edit', compact('post'));
    }

    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'summary' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_active' => 'boolean',
        ]);

        if ($request->title !== $post->title) {
            $data['slug'] = Str::slug($request->title) . '-' . time();
        }

        $data['author'] = $request->input('author', 'Admin');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($post->image && !str_starts_with($post->image, 'http')) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->image && !str_starts_with($post->image, 'http')) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Blog post deleted successfully.');
    }

    public function updateSettings(Request $request)
    {
        $keys = ['blog_title', 'blog_subtitle', 'blog_eyebrow', 'blog_heading'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        return redirect()->back()->with('success', 'Blog header settings updated successfully.');
    }
}

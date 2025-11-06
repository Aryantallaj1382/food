<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->paginate(10);
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/sliders');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $image->move($path, $imageName);
            $finalPath = 'uploads/sliders/' . $imageName;
        }
        Slider::create([
            'image' => $finalPath ?? null,
            'link' => $request->link,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'اسلایدر با موفقیت اضافه شد');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['link', 'order']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/sliders');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $image->move($path, $imageName);
            $finalPath = 'uploads/sliders/' . $imageName;
            $data['image'] = $finalPath;
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'اسلایدر با موفقیت ویرایش شد');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();
        return back()->with('success', 'اسلایدر حذف شد');
    }
}

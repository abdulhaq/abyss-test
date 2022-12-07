<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = Image::paginate(10);
        return response()->json($images);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check if inputs are valid
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required'],
            'file' => ['required', 'mimes:jpg,jpeg,png', 'max:5000'],
            'type' => ['required', 'integer']

        ]);

        //throw error if not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        //upload file to server
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $request->file('file')->extension();
            $name = uniqid() . '.' . $ext;
            $filePath = 'images/' . $name;
            Storage::disk('local')->put($filePath, file_get_contents($file));
        } else {

            return response()->json(['error' => 'File not uploaded']);
        }

        //save data to database
        $image = Image::create([
            'name' => $request->name,
            'description' => $request->description,
            'file' => $name,
            'type' => $request->type
        ]);

        return response()->json($image);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $images = Image::find($id);
        $temp_url = Storage::disk('public')->temporaryUrl('images/' . $images->file, now()->addMinutes(10));

        return response()->json([
            'name' => $images->name,
            'description' => $images->description,
            'type' => $images->type,
            'url' => $temp_url
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //delete records older than 30 days
    public function deleteOldRecords()
    {
        Image::whereDate('created_at', '<=', now()->subDays(30))->delete();
    }
}

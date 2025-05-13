<?php

namespace App\Http\Controllers;

use App\Models\PictureModel;
use Illuminate\Http\Request;

class PictureController extends Controller
{
    public function index()
    {
        return view('admin.picture.add');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'picturename' => 'nullable|string|max:255',
            'picture' => 'required|mimes:jpeg,tiff,tif,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv|max:8096',
        ]);

        // Handle the picture upload if a file is provided
        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $fileExtension = $picture->getClientOriginalExtension();
            $fileName = time() . '.' . $fileExtension;

            // Determine the storage path based on file type
            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // For image files
                $picture->move(public_path('uploads/pictureitems'), $fileName);
                $picturePath = 'uploads/pictureitems/' . $fileName;
            } elseif (in_array($fileExtension, ['pdf','tiff','tif', 'doc', 'docx', 'xls', 'xlsx', 'csv'])) {
                // For document/PDF files
                $picture->move(public_path('uploads/pdfs'), $fileName);
                $picturePath = 'uploads/pdfs/' . $fileName;
            }
        }

        // Create a new PictureModel instance and fill it with validated data
        $pictureModel = new PictureModel([
            'picturename' => $request->input('picturename'),
            'picture' => $picturePath,
        ]);

        // Save the instance to the database
        $pictureModel->save();
        return redirect()->back()->with('success', 'Picture added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function trashshow()
    {
        $pictureitem = PictureModel::onlyTrashed()->get();
        return view('admin.picture.trash')->with('pictureitem', $pictureitem);
    }

    public function show()
    {
        $pictureitem = PictureModel::Paginate(30);
        return view('admin.picture.show')->with('pictureitem', $pictureitem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the item by ID
        if (PictureModel::find($id)) {
            $pictureitem = PictureModel::find($id);
            return view('admin.picture.edit')->with(['pictureitem' => $pictureitem]);
        } else {
            return redirect()->back()->with('failure', 'Picture not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'picturename' => 'nullable|string|max:255',
            'picture' => 'nullable|mimes:jpeg,tiff,tif,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv|max:8096',
        ]);

        // Find the existing item by ID
        $pictureModel = PictureModel::findOrFail($id); // Already throws 404 if not found

        if ($request->hasFile('picture')) {
            // Delete old picture if it exists
            if ($pictureModel->picture && file_exists(public_path($pictureModel->picture))) {
                unlink(public_path($pictureModel->picture));
            }

            // Handle new file upload
            $picture = $request->file('picture');
            $fileExtension = $picture->getClientOriginalExtension();
            $fileName = time() . '.' . $fileExtension;

            // Determine the storage path based on file type
            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // For image files
                $picture->move(public_path('uploads/pictureitems'), $fileName);
                $pictureModel->picture = 'uploads/pictureitems/' . $fileName;
            } elseif (in_array($fileExtension, ['pdf','tif','tiff', 'doc', 'docx', 'xls', 'xlsx', 'csv'])) {
                // For document/PDF files
                $picture->move(public_path('uploads/pdfs'), $fileName);
                $pictureModel->picture = 'uploads/pdfs/' . $fileName;
            }
        }

        // Update other fields
        $pictureModel->picturename = $request->input('picturename');

        // Save model
        $pictureModel->update();

        return redirect()->back()->with('success', 'Picture updated successfully!');
    }

    /**
     * Restore the soft-deleted item.
     */
    public function restore(string $id)
    {
        // Retrieve the soft-deleted item
        $pictureitem = PictureModel::withTrashed()->find($id);

        if ($pictureitem) {
            if ($pictureitem->restore()) {
                return redirect()->back()->with('success', 'Picture restored successfully!');
            } else {
                return redirect()->back()->with('failure', 'Failed to restore the picture.');
            }
        } else {
            return redirect()->back()->with('failure', 'Item not found!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the menu item by ID
        $pictureitem = PictureModel::find($id);

        if ($pictureitem) {
            // Delete the file from the server if it exists
            if ($pictureitem->picture && file_exists(public_path($pictureitem->picture))) {
                unlink(public_path($pictureitem->picture));
            }

            // Delete the record
            $pictureitem->delete();

            return redirect()->back()->with('success', 'Picture Trashed successfully!');
        } else {
            return redirect()->back()->with('failure', 'Picture not found!');
        }
    }

    /**
     * Preview the specified resource.
     */
    public function preview($id)
{
    // Find the picture by ID
    $picture = PictureModel::findOrFail($id);

    // Check if the picture is an image or PDF
    $fileExtension = strtolower(pathinfo($picture->picture, PATHINFO_EXTENSION));

    // If the file is a PDF
    if ($fileExtension == 'pdf') {
        return view('admin.picture.preview', compact('picture'))->with('is_pdf', true);
    }
    // If the file is an image
    else {
        return view('admin.picture.preview', compact('picture'))->with('is_pdf', false);
    }
}


    /**
     * Permanently delete the specified resource.
     */
    public function permdeleted(string $id)
    {
        $pictureitem = PictureModel::withTrashed()->find($id);

        if ($pictureitem) {
            // Permanently delete the item and the associated file
            if ($pictureitem->forceDelete()) {
                // Delete the file from the server if it exists
                if ($pictureitem->picture && file_exists(public_path($pictureitem->picture))) {
                    unlink(public_path($pictureitem->picture));
                }
                return redirect()->back()->with('success', 'Picture deleted permanently!');
            } else {
                return redirect()->back()->with('failure', 'Failed to delete the picture permanently.');
            }
        } else {
            return redirect()->back()->with('failure', 'Item not found!');
        }
    }
}

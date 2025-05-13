@extends('admin/layout2')
@section('main_section')
    <h2>Update Your Picture </h2>
    <form action="{{ route('picture.update', $pictureitem->picture_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Item Name Field -->
        <div class="mb-3">
            <label class="form-label">Picture Name</label>
            <input class="form-control" type="text" name="picturename" value="{{ $pictureitem->picturename }}"
                placeholder="Enter Picture name" />
            @error('picturename')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="row">
            <div class="col-md-3">
                <p>Existing Picture</p>
                <img src="{{ asset($pictureitem->picture) }}" alt="Item Picture" width="100" height="100"
                    style="border-radius: 50%" />
            </div>

            <!-- Picture Field -->
            <div class="mb-3 col-md-9">
                <label class="form-label">Update Picture</label>
                <input class="form-control" type="file" name="picture" />
                @error('picture')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-lg btn-primary">Update Picture</button>
        </div>
    </form>
@endsection

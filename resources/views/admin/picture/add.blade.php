@extends('admin/layout2')

@section('main_section')
<h2>Upload Your Desired Picture or PDF</h2>
<form action="{{ route('picture.add') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Picture Name Field -->
    <div class="mb-3">
        <label class="form-label">Picture Name (Optional)</label>
        <input class="form-control" type="text" name="picturename" value="{{ old('picturename') }}"
            placeholder="Enter Pic Name" />
        @error('picturename')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Picture Field -->
    <div class="mb-3">
        <label class="form-label">Picture</label>
        <input class="form-control" type="file" name="picture" accept="image/*,application/pdf" />
        @error('picture')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-lg btn-primary">Add Item</button>
    </div>
</form>
@endsection

@extends('admin.layout2')

@section('main_section')

<div class="container">
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <h3 class="mb-0"> All Pictures </h3>
            <a href="{{ route('picture.add') }}" class="btn btn-primary">Add New Picture</a>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped  table-responsive col-12">
    <thead>
        <tr>
            <th>Picture ID</th>
            <th>Picture Name</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pictureitem as $item)
        <tr>
            <td>{{ $item->picture_id }}</td>
            <td>{{ $item->picturename }}</td>
            
            <td>
                <a href="{{ route('picture.edit', $item->picture_id) }}" class="btn btn-sm btn-primary">Edit</a>
                <a href="{{ route('picture.delete', $item->picture_id) }}" class="btn btn-sm btn-danger">Trash</a>
                <a href="{{ route('picture.preview', $item->picture_id) }}" target="_self" class="btn btn-sm btn-secondary">Preview</a>
            <td>
            
        </tr>

        
        @endforeach
    </tbody>
</table>
{{ $pictureitem->links('pagination::bootstrap-5') }}

@endsection

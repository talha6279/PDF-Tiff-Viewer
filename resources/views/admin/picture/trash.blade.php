@extends('admin.layout2')

@section('main_section')

<div class="container">
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Deleted  Pictures</h3>
            <a href="{{ route('picture.add') }}" class="btn btn-primary">Add Picture</a>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped  table-responsive col-12">
    <thead>
        <tr>
            <th>Picture ID</th>
            <th>Picture Name</th>
            <th>Picture</th>
            <th colspan="2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pictureitem as $item)
        <tr>
            <td>{{ $item->picture_id }}</td>
            <td>{{ $item->picturename }}</td>
            <td>
                @if($item->picture)
                    <img src="{{ asset($item->picture) }}" alt="{{ $item->itemname }}" width="100" height="80" style="border-radius: 50%">
                @else
                    No image available
                @endif
            </td>
            <td>
                <a href="{{ route('picture.restore', $item->picture_id) }}" class="btn btn-sm btn-primary">Restore</a>
            </td>
            <td>
                <a href="{{ route('picture.permdeleted', $item->picture_id) }}" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>

        @endforeach
    </tbody>
</table>

@endsection

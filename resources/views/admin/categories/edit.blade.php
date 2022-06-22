@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.category.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.categories.update", [$category->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.category.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{$category->name}}" required>
                @if($errors->has('name'))
                <em class="invalid-feedback">
                    {{ $errors->first('name') }}
                </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.category.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('pid') ? 'has-error' : '' }}">
                <label for="name">Parent* </label>
                <select name="pid" id="pid" class="form-control">
                    <option value="0" {{$category->pid === "0" ? "selected" : ""}}>Top level</option>
                    @foreach($parentCategories as $parentCategory)
                    <option value="{{$parentCategory->id}}" {{$parentCategory->id === $category->pid ? "selected" : ""}}>{{$parentCategory->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection
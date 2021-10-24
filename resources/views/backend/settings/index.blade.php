@extends('layouts/admin')

@section('content')

<div class="row">
    <div class="col-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span class="m-0 font-weight-bold text-primary">Mange Settings</span>
            </div>
            <ul class="list-group list-group-flash">
                @foreach ($settings_sections as $setting_section)
                <li class="list-group-item">
                    <a class="nav-link" href="{{ route('admin.settings.index', $setting_section) }}"> <i
                            class="fa fa-gear"></i> {{ $setting_section }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-9">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span class="m-0 font-weight-bold text-primary">Settings {{ $section }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update', 1) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @foreach ($settings as $setting)
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="title">{{ $setting->display_name }}</label>
                                @if ($setting->type == 'text')
                                <input type="text" name="value[{{ $loop->index }}]" id="value" class="form-control"
                                    value="{{ $setting->value }}">

                                @elseif($setting->type == 'textarea')
                                <textarea name="value[{{ $loop->index }}]" id="value" class="form-control" cols="30"
                                    rows="10">{{ $setting->value }}</textarea>

                                @elseif($setting->type == 'image')
                                <div class="custom-file mb-3">
                                    <input type="file" name="value[{{ $loop->index }}]" class="custom-file-input"
                                        id="value">
                                    <label class="custom-file-label" for="value">Choose file...</label>
                                </div>

                                @elseif($setting->type == 'select')
                                {!! Form::select('value[' . $loop->index . ']', explode('|', $setting->details) ,
                                $setting->value , ['id' => 'value', 'class' => 'form-control']) !!}

                                @elseif($setting->type == 'checkbox')
                                {!! Form::checkbox('value[' . $loop->index . ']', 1, $setting->value == 1 ? true : false
                                , ['id' => 'value', 'class' => 'styled']) !!}

                                @elseif($setting->type == 'radio')
                                {!! Form::radio('value[' . $loop->index . ']', 1, $setting->value == 1 ? true : false ,
                                ['id' => 'value', 'class' => 'styled']) !!}

                                @endif

                                <input type="hidden" name="key[{{ $loop->index }}]" id="key" class="form-control"
                                    value="{{ $setting->key }}" readonly>
                                <input type="hidden" name="id[{{ $loop->index }}]" id="key" class="form-control"
                                    value="{{ $setting->id }}" readonly>
                                <input type="hidden" name="ordering[{{ $loop->index }}]" id="key" class="form-control"
                                    value="{{ $setting->ordering }}" readonly>

                                @error('value') <span class="text-danger">{{ $message }}</span>@enderror

                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="">
                        <input type="submit" class="btn btn-outline-dark" value="Save">
                    </div>
                </form>
            </div>

            {{-- Filter Filds
            @include('backend.users.filter.filter') --}}


        </div>
    </div>
</div>

@endsection
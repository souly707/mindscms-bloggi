@extends('layouts.admin')
@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex">
        <h6 class="m-0 font-weight-bold text-dark">({{ $message->title }})</h6>
        <div class="ml-auto">
            <a href="{{ route('admin.contact_us.index') }}" class="btn btn-outline-primary">
                <span class="icon">
                    <i class="fa fa-home"></i>
                </span>
                <span class="text">Contact Us</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th>Title</th>
                    <td>{{ $message->title }}</td>
                </tr>

                <tr>
                    <th>From</th>
                    <td>{{ $message->name }} <span class="text-muted">
                            <{{ $message->email }}>
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Message</th>
                    <td>{{ $message->message }}</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

@endsection
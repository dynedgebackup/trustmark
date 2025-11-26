@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">MANAGE USER</h3>

        <a class="btn btn-primary btn-sm" href="{{ route('user.create') }}"
            style="font-size: 12px; font-family: sans-serif;">Add</a>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>User List</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                    </div>
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                        aria-describedby="dataTable_info">
                        <table class="table my-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="custom-th">No</th>
                                    <th class="custom-th">Full Name</th>
                                    {{-- <th class="custom-th">Username</th> --}}
                                    <th class="custom-th">Email</th>
                                    <th class="custom-th">Mobile No.</th>
                                    <th class="custom-th">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admins as $admin)
                                    <tr>
                                        <td class="custom-td">{{ $loop->iteration }}</td>
                                        <td class="custom-td">{{ $admin->name }}</td>
                                        {{-- <td class="custom-td">{{ $admin->username }}</td> --}}
                                        <td class="custom-td">{{ $admin->email ?? '-' }}</td>
                                        <td class="custom-td">{{ $admin->ctc_no ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('user.view', encrypt($admin->id)) }}" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Edit">
                                                <i class="custom-pencil-icon fa fa-pencil-square-o"></i>
                                            </a>

                                            <i class="far fa-trash-alt icon-btn delete-icon" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-bs-placement="right"
                                                data-bs-toggle="tooltip" title="Delete" data-id="{{ $admin->id }}"></i>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        @if (Auth::check() && in_array(Auth::user()->role, [2]))
                                            <td class="custom-td text-center" colspan="6">No admins
                                                found.</td>
                                        @endif
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" tabindex="-1" id="deleteModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content custom-modal-content">
                        <div class="modal-header custom-modal-header">
                            <h4 class="modal-title">Delete</h4>
                            <button class="btn-close" aria-label="Close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <div class="modal-body custom-modal-body">
                            <p>Are you sure you want to delete this?</p>
                        </div>
                        <div class="modal-footer custom-modal-footer">
                            <button class="btn btn-light" data-bs-dismiss="modal" type="button">Close</button>

                            <!-- No $admin->id here; will be set by JS -->
                            <form id="deleteForm" method="POST" action="">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.delete-icon').on('click', function() {
                const adminId = $(this).data('id');
                const actionUrl = '{{ route('user.destroy', ':id') }}'.replace(':id', adminId);
                $('#deleteForm').attr('action', actionUrl);
            });
        });
    </script>
@endsection

<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Category List</h2>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="{{ route('new-categories.create') }}" class="btn btn-primary">Add Category</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th class="desc-column">Description</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td class="desc-column" title="{{ $category->description }}">
                                                {{ Str::limit($category->description, 50, '...') }}
                                            </td>
                                            <td>{{ $category->status ? 'Active' : 'Inactive' }}</td>
                                            <td>{{ $category->order_by }}</td>
                                            <td>
                                                <a href="{{ route('new-categories.edit', ['new_category' => $category->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('new-categories.destroy', ['new_category' => $category->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <style>
                            .desc-column {
                                max-width: 250px;
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                            }

                            @media (max-width: 768px) {
                                .desc-column {
                                    max-width: 100px;
                                }
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

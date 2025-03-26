<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2>Edit Category</h2>
                        
                        <form action="{{ route('new-categories.update', ['new_category' => $category->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control">{{ $category->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $category->status == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $category->status == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Order</label>
                                <input type="number" name="order_by" class="form-control" value="{{ $category->order_by }}" required>
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('new-categories.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

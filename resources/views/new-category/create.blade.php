<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2>Add Category</h2>
                        
                        <form action="{{ route('new-categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Order</label>
                                <input type="number" name="order_by" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="{{ route('new-categories.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

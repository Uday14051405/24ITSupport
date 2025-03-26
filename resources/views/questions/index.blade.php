<x-master-layout>
    <div class="container">
        <h2 class="text-center">Question List</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('questions.create') }}" class="btn btn-primary">Add Question</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Category</th>
                        <th>Question</th>
                        <th>Input Type</th>
                        <th>Is Required</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->category->name }}</td>
                            <td>{{ $question->question }}</td>
                            <td>{{ ucfirst($question->input_type) }}</td>
                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>
                            <td>{{ $question->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="d-inline">
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
    </div>
</x-master-layout>

<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Edit Question</h2>

                        <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="question_category_id">Select Category</label>
                                <select name="question_category_id" id="question_category_id" class="form-control" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $question->question_category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="question">Question</label>
                                <input type="text" name="question" id="question" class="form-control" value="{{ $question->question }}" required>
                            </div>

                            <div class="form-group">
                                <label for="input_type">Input Type</label>
                                <select name="input_type" id="input_type" class="form-control" required>
                                    <option value="text" {{ $question->input_type == 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="textarea" {{ $question->input_type == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                    <option value="email" {{ $question->input_type == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="tel" {{ $question->input_type == 'tel' ? 'selected' : '' }}>Telephone</option>
                                    <option value="number" {{ $question->input_type == 'number' ? 'selected' : '' }}>Number</option>
                                    <option value="date" {{ $question->input_type == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="datetime-local" {{ $question->input_type == 'datetime-local' ? 'selected' : '' }}>Date & Time</option>
                                    <option value="time" {{ $question->input_type == 'time' ? 'selected' : '' }}>Time</option>
                                    <option value="file" {{ $question->input_type == 'file' ? 'selected' : '' }}>File Upload</option>
                                    <option value="radio" {{ $question->input_type == 'radio' ? 'selected' : '' }}>Radio Button</option>
                                    <option value="checkbox" {{ $question->input_type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                    <option value="select" {{ $question->input_type == 'select' ? 'selected' : '' }}>Select</option>
                                </select>
                            </div>

                            <!-- Dynamic Options List -->
                            <div class="form-group" id="options-container" style="display: none;">
                                <label>Options</label>
                                <div class="input-group mt-2">
                                    <input type="text" id="new-option" class="form-control" placeholder="Add new option">
                                    <div class="input-group-append">
                                        <button type="button" id="add-option" class="btn btn-primary">Add</button>
                                    </div>
                                </div>

                                <ul id="options-list" class="list-group">
                                    @if($question->options)
                                        @foreach($question->options as $option)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="option-text" style="color: grey;">{{ $option }}</span>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-warning edit-option">Edit</button>
                                                <button type="button" class="btn btn-sm btn-danger delete-option">Delete</button>
                                            </div>
                                        </li>
                                        @endforeach
                                    @endif
                                </ul>

                                <!-- Hidden field to store options as JSON -->
                                <input type="hidden" name="options" id="options-hidden">
                            </div>

                            <div class="form-group">
                                <label>Is Required?</label>
                                <div class="form-check">
                                    <input type="radio" name="is_required" id="required_yes" value="1" class="form-check-input" {{ $question->is_required ? 'checked' : '' }}>
                                    <label for="required_yes" class="form-check-label">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="is_required" id="required_no" value="0" class="form-check-input" {{ !$question->is_required ? 'checked' : '' }}>
                                    <label for="required_no" class="form-check-label">No</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1" {{ $question->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$question->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Question</button>
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let inputType = document.getElementById('input_type');
            let optionsContainer = document.getElementById('options-container');
            let optionsList = document.getElementById('options-list');
            let newOptionInput = document.getElementById('new-option');
            let addOptionBtn = document.getElementById('add-option');
            let optionsHidden = document.getElementById('options-hidden');

            function toggleOptionsField() {
                let selectedType = inputType.value;
                optionsContainer.style.display = ['radio', 'checkbox', 'select'].includes(selectedType) ? 'block' : 'none';
            }

            function updateHiddenField() {
                let options = [];
                document.querySelectorAll("#options-list .option-text").forEach(el => {
                    options.push(el.innerText);
                });
                optionsHidden.value = JSON.stringify(options);
            }

            addOptionBtn.addEventListener("click", function() {
                let optionValue = newOptionInput.value.trim();
                if (optionValue) {
                    let listItem = document.createElement("li");
                    listItem.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
                    listItem.innerHTML = `
                        <span class="option-text" style="color: grey;">${optionValue}</span>
                        <div>
                            <button type="button" class="btn btn-sm btn-warning edit-option">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger delete-option">Delete</button>
                        </div>
                    `;
                    optionsList.appendChild(listItem);
                    newOptionInput.value = "";
                    updateHiddenField();
                }
            });

            optionsList.addEventListener("click", function(event) {
                if (event.target.classList.contains("delete-option")) {
                    event.target.closest("li").remove();
                    updateHiddenField();
                } else if (event.target.classList.contains("edit-option")) {
                    let listItem = event.target.closest("li");
                    let textSpan = listItem.querySelector(".option-text");
                    let newValue = prompt("Edit option:", textSpan.innerText);
                    if (newValue !== null) {
                        textSpan.innerText = newValue.trim();
                        updateHiddenField();
                    }
                }
            });

            toggleOptionsField();
            inputType.addEventListener('change', toggleOptionsField);
            updateHiddenField();
        });
    </script>
    @endsection
</x-master-layout>

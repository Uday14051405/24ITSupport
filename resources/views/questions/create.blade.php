<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Add New Question</h2>

                        <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" id="question-form">
                            @csrf
                            <div class="form-group">
                                <label for="question_category_id">Select Category</label>
                                <select name="question_category_id" id="question_category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="question">Question</label>
                                <input type="text" name="question" id="question" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="input_type">Input Type</label>
                                <select name="input_type" id="input_type" class="form-control" required>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Telephone</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="datetime-local">Date & Time</option>
                                    <option value="time">Time</option>
                                    <option value="file">File Upload</option>
                                    <option value="radio">Radio Button</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="select">Select</option>
                                </select>
                            </div>

                            <div class="form-group" id="options-container" style="display: none;">
                                <label>Options</label>
                                <div class="input-group">
                                    <input type="text" id="option-input" class="form-control" placeholder="Enter option">
                                    <div class="input-group-append">
                                        <button type="button" id="add-option" class="btn btn-primary">Add</button>
                                    </div>
                                </div>
                                <ul id="options-list" class="mt-2 list-group"></ul>
                                <input type="hidden" name="options" id="options-hidden">
                            </div>

                            <div class="form-group">
                                <label>Is Required?</label>
                                <div class="form-check">
                                    <input type="radio" name="is_required" id="required_yes" value="1" class="form-check-input">
                                    <label for="required_yes" class="form-check-label">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="is_required" id="required_no" value="0" class="form-check-input" checked>
                                    <label for="required_no" class="form-check-label">No</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Save Question</button>
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
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
        let optionInput = document.getElementById('option-input');
        let addOptionBtn = document.getElementById('add-option');
        let optionsHiddenInput = document.getElementById('options-hidden');
        let questionForm = document.getElementById('question-form');
        let optionsArray = [];

        function toggleOptionsField() {
            optionsContainer.style.display = ['radio', 'checkbox', 'select'].includes(inputType.value) ? 'block' : 'none';
            if (!['radio', 'checkbox', 'select'].includes(inputType.value)) {
                optionsArray = [];
                renderOptions();
            }
        }

        function renderOptions() {
            optionsList.innerHTML = "";
            optionsArray.forEach((option, index) => {
                let li = document.createElement("li");
                li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
                li.innerHTML = `
                    <span class="option-text" style="color: grey;">${option}</span>
                    <span>
                        <button type="button" class="btn btn-sm btn-warning edit-option" data-index="${index}">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger delete-option" data-index="${index}">Delete</button>
                    </span>
                `;
                optionsList.appendChild(li);
            });

            optionsHiddenInput.value = JSON.stringify(optionsArray);

            document.querySelectorAll(".edit-option").forEach(button => {
                button.addEventListener("click", function() {
                    let index = this.getAttribute("data-index");
                    editOption(index);
                });
            });

            document.querySelectorAll(".delete-option").forEach(button => {
                button.addEventListener("click", function() {
                    let index = this.getAttribute("data-index");
                    deleteOption(index);
                });
            });
        }

        function editOption(index) {
            let newOption = prompt("Edit Option:", optionsArray[index]);
            if (newOption !== null && newOption.trim() !== "") {
                optionsArray[index] = newOption.trim();
                renderOptions();
            }
        }

        function deleteOption(index) {
            if (confirm("Are you sure you want to delete this option?")) {
                optionsArray.splice(index, 1);
                renderOptions();
            }
        }

        addOptionBtn.addEventListener("click", function() {
            let newOption = optionInput.value.trim();
            if (newOption !== "") {
                optionsArray.push(newOption);
                optionInput.value = "";
                renderOptions();
            }
        });

        questionForm.addEventListener("submit", function(event) {
            if (['radio', 'checkbox', 'select'].includes(inputType.value) && optionsArray.length === 0) {
                event.preventDefault();
                alert("Please add at least one option.");
            }
        });

        inputType.addEventListener('change', toggleOptionsField);
        toggleOptionsField();
    });
    </script>
    @endsection
</x-master-layout>

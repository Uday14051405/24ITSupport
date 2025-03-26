<x-master-layout>
    <style>
        .form-control{
            border: var(--bs-border-width) solid #aea6a6;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <center><h2>Questionnaire</h2></center><br>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('questions.save') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $userId }}">

                            @foreach($categories as $index => $category)
                                <div class="category-section mb-4">
                                    <h3>{{ $category->name }}</h3>
                                    <p>{{ $category->description }}</p>

                                    @php $questionNumber = 1; @endphp  

                                    @foreach($category->questions as $question)
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-md-4"><strong>Q. {{ $questionNumber }}:</strong> {{ $question->question }}<span style="color: red">{{ $question->is_required ? '*' : '' }}</span></label>

                                            @php
                                                $savedAnswer = $userAnswers[$question->id] ?? '';
                                                $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                $isRequired = $question->is_required ? 'required' : '';
                                            @endphp

                                            <div class="col-md-8">
                                                @if($question->input_type == 'text')
                                                    <input type="text" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'textarea')
                                                    <textarea name="answers[{{ $question->id }}]" class="form-control" {{ $isRequired }}>{{ $savedAnswer }}</textarea>

                                                @elseif($question->input_type == 'email')
                                                    <input type="email" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'tel')
                                                    <input type="tel" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'number')
                                                    <input type="number" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'date')
                                                    <input type="date" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'datetime-local')
                                                    <input type="datetime-local" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'time')
                                                    <input type="time" name="answers[{{ $question->id }}]" class="form-control" value="{{ $savedAnswer }}" {{ $isRequired }}>

                                                @elseif($question->input_type == 'file')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file" name="answers[{{ $question->id }}]" class="form-control w-auto" 
                                                            {{ ($isRequired == 'required' && empty($savedAnswer)) ? 'required' : '' }}>
                                                        
                                                        @if($savedAnswer)
                                                            <a href="{{ Storage::url($savedAnswer) }}" target="_blank" class="btn btn-primary btn-sm fs-5">View File</a>
                                                        @endif
                                                    </div>



                                                @elseif($question->input_type == 'radio' && is_array($options))
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($options as $option)
                                                            <div class="form-check me-3">
                                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="form-check-input" {{ $savedAnswer == $option ? 'checked' : '' }} {{ $isRequired }}>
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                @elseif($question->input_type == 'checkbox' && is_array($options))
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($options as $option)
                                                            <div class="form-check me-3">
                                                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" class="form-check-input" {{ in_array($option, json_decode($savedAnswer, true) ?? []) ? 'checked' : '' }} {{ $isRequired }}>
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                @elseif($question->input_type == 'select' && is_array($options))
                                                    <select name="answers[{{ $question->id }}]" class="form-control" {{ $isRequired }}>
                                                        <option value="">Select an option</option>
                                                        @foreach($options as $option)
                                                            <option value="{{ $option }}" {{ $savedAnswer == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                        @php $questionNumber++; @endphp
                                    @endforeach
                                </div>

                                <!-- Add a line after each category except the last one -->
                                @if($index !== count($categories) - 1)
                                    <hr class="my-4" style="border: 1px solid grey;">
                                @endif
                            @endforeach

                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

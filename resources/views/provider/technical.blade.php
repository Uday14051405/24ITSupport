<x-master-layout>
    <style>
        .form-control {
            border: var(--bs-border-width) solid #aea6a6;
        }
    </style>
    <div class="container-fluid">
        @include('partials._provider')

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $providerdata->first_name . ' ' . $providerdata->last_name }} - {{ $pageTitle }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('provider.technical-data.save', ['id' => $providerdata->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @foreach($categories as $index => $category)
                                <div class="category-section mb-4">
                                    <h3>{{ $category->name }}</h3>
                                    <p>{{ $category->description }}</p>

                                    @php $questionNumber = 1; @endphp  

                                    @foreach($category->questions as $question)
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-md-4"><strong>Q. {{ $questionNumber }}:</strong> {{ $question->question }}</label>

                                            @php
                                                $savedAnswer = $userAnswers[$question->id] ?? '';
                                            @endphp

                                            <div class="col-md-8 d-flex align-items-center gap-2">
                                                @if(in_array($question->input_type, ['text', 'email', 'number', 'tel', 'date', 'time', 'datetime-local']))
                                                    <input type="{{ $question->input_type }}" class="form-control" name="answers[{{ $question->id }}]" value="{{ $savedAnswer }}">

                                                @elseif($question->input_type == 'textarea')
                                                    <textarea class="form-control" name="answers[{{ $question->id }}]">{{ $savedAnswer }}</textarea>

                                                @elseif($question->input_type == 'file')
                                                    <input type="file" class="form-control" name="answers[{{ $question->id }}]">
                                                    @if($savedAnswer)
                                                        <a href="{{ Storage::url($savedAnswer) }}" target="_blank" class="btn btn-primary btn-sm fs-5">View File</a>
                                                    @endif

                                                @elseif($question->input_type == 'radio')
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                    @endphp
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($options as $option)
                                                            <div class="form-check me-3">
                                                                <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="{{ $option }}" {{ $savedAnswer == $option ? 'checked' : '' }}>
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                @elseif($question->input_type == 'checkbox')
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                        $savedAnswers = is_string($savedAnswer) ? json_decode($savedAnswer, true) : (is_array($savedAnswer) ? $savedAnswer : []);
                                                    @endphp
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($options as $option)
                                                            <div class="form-check me-3">
                                                                <input type="checkbox" class="form-check-input" name="answers[{{ $question->id }}][]" value="{{ $option }}" {{ in_array($option, $savedAnswers) ? 'checked' : '' }}>
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                @elseif($question->input_type == 'select')
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                    @endphp
                                                    <select class="form-control" name="answers[{{ $question->id }}]">
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

                                @if($index !== count($categories) - 1)
                                    <hr class="my-4" style="border: 1px solid grey;">
                                @endif
                            @endforeach

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>

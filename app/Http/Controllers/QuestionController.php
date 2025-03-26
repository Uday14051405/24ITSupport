<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Category;
use App\Models\UserServiceAnswer;
use Illuminate\Http\Request;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::with('category')->get();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = QuestionCategory::all();
        return view('questions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ensure options are properly formatted
        $options = $request->options ? json_decode($request->options, true) : null;

        // Validate the request
        $request->validate([
            'question_category_id' => 'required|exists:question_categories,id',
            'question' => 'required|string|max:255',
            'input_type' => 'required|string|in:text,textarea,email,tel,number,date,datetime-local,time,file,radio,checkbox,select',
            'options' => ['nullable', 'json'], // Ensure it's valid JSON when provided
            'status' => 'required|in:0,1',
            'is_required' => 'required|in:0,1',
        ]);

        // Store options only if input type requires it
        if (!in_array($request->input_type, ['radio', 'checkbox', 'select'])) {
            $options = null; // Remove options if not needed
        }

        // Create question
        Question::create([
            'question_category_id' => $request->question_category_id,
            'question' => $request->question,
            'input_type' => $request->input_type,
            'options' => $options, // Laravel automatically converts arrays to JSON
            'status' => $request->status,
            'is_required' => $request->is_required,
        ]);

        return redirect()->route('questions.index')->with('success', 'Question added successfully.');
    }





    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $categories = QuestionCategory::all();
        
        // Decode options JSON (handle null case)
        $question->options = is_array($question->options) ? $question->options : json_decode($question->options, true);

        return view('questions.edit', compact('question', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $options = $request->options ? json_decode($request->options, true) : null;
        
        $request->validate([
            'question_category_id' => 'required|exists:question_categories,id',
            'question' => 'required|string|max:255',
            'input_type' => 'required|string',
            'options' => ['nullable', 'json'],
            'status' => 'required|boolean',
            'is_required' => 'required|boolean',
        ]);

        // Store options only for select, checkbox, or radio inputs
        if (!in_array($request->input_type, ['radio', 'checkbox', 'select'])) {
            $options = null; 
        }
        //  $options = in_array($request->input_type, ['radio', 'checkbox', 'select']) 
                // ? (is_array($request->options) ? json_encode($request->options, JSON_UNESCAPED_UNICODE) : $request->options) 
                // : null;


        $question->update([
            'question_category_id' => $request->question_category_id,
            'question' => $request->question,
            'input_type' => $request->input_type,
            'options' => $options,
            'status' => $request->status,
            'is_required' => $request->is_required,
        ]);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }

    public function showQuestionForm()
    {
        $userId = Auth::id(); // Get the logged-in user's ID

        // Fetch categories sorted by order_by and only include questions with status = 1
        $categories = QuestionCategory::orderBy('order_by', 'asc')
            ->with(['questions' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();

        // Fetch user answers if they exist
        $userAnswers = UserAnswer::where('user_id', $userId)->pluck('answer', 'question_id')->toArray();

        return view('questions.form', compact('categories', 'userAnswers', 'userId'));
    }

    // Handle Form Submission
    public function saveAnswers(Request $request)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);

        // Log incoming request
        Log::info('File Upload Attempt:', ['user_id' => $userId, 'request' => $request->all()]);

        // Ensure 'uploads' directory exists
        Storage::disk('public')->makeDirectory('uploads');

        // Fetch required questions
        $requiredQuestions = Question::where('is_required', 1)->pluck('input_type', 'id')->toArray();
        
        // Fetch all file-type questions (both required & non-required)
        $fileQuestions = Question::where('input_type', 'file')->pluck('id')->toArray();

        // Validation rules
        $rules = [];
        $messages = [];

        // Apply required validation only for required questions
        foreach ($requiredQuestions as $questionId => $inputType) {
            if ($inputType === 'file') {
                $rules["answers.$questionId"] = 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048';
                $messages["answers.$questionId.file"] = "Invalid file type.";
            } else {
                $rules["answers.$questionId"] = 'required';
                $messages["answers.$questionId.required"] = "This field is required.";
            }
        }

        $request->validate($rules, $messages);

        // Process file uploads for ALL file-type questions (required & non-required)
        foreach ($fileQuestions as $questionId) {
            if ($request->hasFile("answers.$questionId")) {
                $file = $request->file("answers.$questionId");

                if (!$file->isValid()) {
                    Log::error("Invalid file detected", ['question_id' => $questionId]);
                    return redirect()->back()->with('error', 'File upload failed.');
                }

                // Store the file in 'uploads' directory inside 'storage/app/public'
                $path = $file->store('uploads', 'public');

                // Log the successful upload
                Log::info("File stored successfully", ['path' => $path]);

                // Save the file path as the answer in the database
                UserAnswer::updateOrCreate(
                    ['user_id' => $userId, 'question_id' => $questionId],
                    ['answer' => $path]
                );
            }
        }

        // Process non-file answers
        foreach ($answers as $questionId => $answer) {
            // Skip if it's a file question (already handled)
            if (in_array($questionId, $fileQuestions)) {
                continue;
            }

            if (is_array($answer)) {
                $answer = json_encode($answer, JSON_UNESCAPED_UNICODE);
            }

            UserAnswer::updateOrCreate(
                ['user_id' => $userId, 'question_id' => $questionId],
                ['answer' => $answer]
            );
        }

        return redirect()->back()->with('success', 'Your answers have been saved.');
    }

    public function showServiceQuestionnaire()
    {
        $userId = Auth::id();

        // Fetch categories where status = 1
        $categories = Category::where('status', 1)->get();

        // Fetch user answers if they exist
        $userAnswers = UserServiceAnswer::where('user_id', $userId)->pluck('answer', 'category_id')->toArray();

        return view('questions.service-form', compact('categories', 'userAnswers', 'userId'));
    }

    public function saveServiceAnswers(Request $request)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);

        foreach ($answers as $categoryId => $answer) {
            UserServiceAnswer::updateOrCreate(
                ['user_id' => $userId, 'category_id' => $categoryId],
                ['answer' => $answer]
            );
        }

        return redirect()->back()->with('success', 'Your responses have been saved.');
    }

}

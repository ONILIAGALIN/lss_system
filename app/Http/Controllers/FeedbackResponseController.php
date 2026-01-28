<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Feedback_response;
use App\Models\Feedback_question;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\FeedbackQuestion;
class FeedbackResponseController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "question_id" => "required|exists:feedback_questions,id",
            "choice_id" => "required|exists:feedback_choices,id",
            "comment" => "sometimes|string:min:2"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation.",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        if(Feedback_response::where('user_id', $validated['user_id'])
            ->where('question_id', $validated['question_id'])
            ->exists()){
                return response()->json([
                    'ok' => false,
                    'message' => 'You have already answered this question.'
                ], 400);
        }
        $response = Feedback_response::create([
            "user_id" => $validated ["user_id"],
            "question_id" => $validated ["question_id"],
            "choice_id" => $validated ["choice_id"],
            "comment" => $validated ["comment"] ?? null,
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Feedback response created successfully",
            "data" => $response
        ],201);
    }

    public function index (Request $request){
        $perPage = $request->get('perPage', 10);
        $response = Feedback_response::with(["user","question","choice"])->paginate($perPage);
        return response()->json([
           "ok" => true,
           "message" => "Feedback response retrieved successfully",
           "data" => $response
        ],200);
    }

    public function show (Request $request, Feedback_response $response){
        return response()->json([
            "ok" => true,
            "message" => "Specific Feedback response retrieved successfully",
            "data" => $response
        ],200); 
    }

    public function update (Request $request, Feedback_response $response){
        $validator = Validator::make($request->all(),[
            "user_id" => "sometimes|exists:users,id",
            "question_id" => "sometimes|exists:questions,id",
            "choice_id" => "sometimes|exists:choices,id",
            "comment" => "nullable|string|min:2"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Failed to update feedback response",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
       /* $response->update([
            "user_id" => $validated ["user_id"],
            "question_id" => $validated ["question_id"],
            "choice_id" => $validated ["choice_id"]
        ]);
        */
        $updateData = array_filter($validated, fn($v) => !is_null($v));
        $response->update($updateData);
        return response()->json([
            "ok" => true,
            "message" => "Feedback response update successfully",
            "data" => $response
        ],200);
    }

    public function destroy (Request $request, Feedback_response $response){
        $response->delete();
        return response()->json([
            "ok" => true,
            "message" => "Feedback response deleted successfully"
        ],205);
    }

     public function exportFeedback(){
        $questions = Feedback_question::with(['choices.responses'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $sheet->setCellValue('A1', 'Question ID');
        $sheet->setCellValue('B1', 'Question');
        $sheet->setCellValue('C1', 'Choice ID');
        $sheet->setCellValue('D1', 'Choice Label');
        $sheet->setCellValue('E1', 'Response ID');
        $sheet->setCellValue('F1', 'User ID');
        $sheet->setCellValue('G1', 'Comment');

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(30);

        $row = 2;

        foreach ($questions as $question) {

        $choices = $question->choices;
        $questionStartRow = $row;

        if($choices->isEmpty()) {
            $sheet->setCellValue("A{$row}", $question->id);
            $sheet->setCellValue("B{$row}", $question->question);
            $row++;
            continue;
        }

        foreach ($choices as $choice) {
            $responses = $choice->responses;
            $choiceStartRow = $row;
            if ($responses->isEmpty()) {
                $sheet->setCellValue("A{$row}", $question->id);
                $sheet->setCellValue("B{$row}", $question->question);
                $sheet->setCellValue("C{$row}", $choice->id);
                $sheet->setCellValue("D{$row}", $choice->label);
                $row++;
                continue;
            }
            foreach ($responses as $response) {
                $sheet->setCellValue("A{$row}", $question->id);
                $sheet->setCellValue("B{$row}", $question->question);
                $sheet->setCellValue("C{$row}", $choice->id);
                $sheet->setCellValue("D{$row}", $choice->label);
                $sheet->setCellValue("E{$row}", $response->id);
                $sheet->setCellValue("F{$row}", $response->user_id);
                $sheet->setCellValue("G{$row}", $response->comment);
                $row++;
            }
            if($responses->count() > 1) {
                $sheet->mergeCells("C{$choiceStartRow}:C" . ($row - 1));
                $sheet->mergeCells("D{$choiceStartRow}:D" . ($row - 1));
                $sheet->getStyle("C{$choiceStartRow}:D" . ($row - 1))
                ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        }

        $questionEndRow = $row - 1;
        if($questionEndRow > $questionStartRow) {
            $sheet->mergeCells("A{$questionStartRow}:A{$questionEndRow}");
            $sheet->mergeCells("B{$questionStartRow}:B{$questionEndRow}");
            $sheet->getStyle("A{$questionStartRow}:B{$questionEndRow}")
                ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="feedback_report.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;
        }
}
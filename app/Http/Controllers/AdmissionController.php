<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\Universitys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AdmissionController extends Controller
{
    //apply for admission

    public function applyAdmission(Request $request, $id)
    {

        $user = $request->user();
        $course = Courses::where('id', $id)->first();
        if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found",
            ];
            return response()->json($response, 200);

        }

        $college = Universitys::where('id', $course['college_id'])->first();

        $admissionRecord = Admission::where('courseId', $id)->where('studentId', $user['id'])->first();

        if ($admissionRecord) {
            $response = [
                'success' => false,
                'message' => "apply already",
            ];
            return response()->json($response, 400);

        }

        $admission = Admission::create([
            'studentId' => $user['id'],
            'courseId' => $course['id'],
            'collegeId' => $college['id'],
            // 'payment_status'=>$request->payment_status,
            // 'admission_status'=>$request->admission_status
        ]);
        $response = [

            'success' => true,
            'message' => "apply successfully",
            $admission,
        ];
        return response()->json($response, 201);
    }

    //get all admission request

    public function getAdmission(Request $request)
    {

        $user = $request->user();
        
        $college = Universitys::where('create-by', $user['id'])->first();
        if(!$college){
            $response = [
                'success' => false,
                'message'=>'college not found'
            ];
            return response()->json($response, 200);
        }

        $admissions = DB::table('admissions')->where('collegeId', $college['id'])->get();

        $response = [
            'success' => true,
            $admissions,

        ];
        return response()->json($response, 200);
    }


    //get admission details
    public function getAdmissionDetails(Request $request, $id)
    {

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if(!$college){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 200);
        }

        $admission = Admission::where('id', $id)->where('collegeId',$college['id'])->first();

        if ($admission) {

            $response = [
                'success' => true,
                $admission,

            ];
            return response()->json($response, 200);
        }
    
        $response = [
            'success' => false,
            'message' => 'not found'

        ];
        return response()->json($response, 200);

    }
    //update admission status

    public function updateAdmissionStatus(Request $request, $id)
    {
        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if(!$college){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 200);
        }

        $admission = Admission::where('id', $id)->where('collegeId',$college['id'])->first();

        if(!$admission){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 200);
        }

        $validator = Validator::make($request->all(), [

            'admission_status' => 'required',

        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        $admission->admission_status = $request->admission_status;

        $admission->save();

        $response = [
            'success' => true,
            $admission
        ];
        return response()->json($response, 400);
    }

}
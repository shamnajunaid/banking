<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Loan;

class AdminController extends Controller
{



  //New Customer Registration

   public function registerCustomer(Request $request){
    
   	 $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
    ]);

     User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 2,
                    'password' => Hash::make($request->input('password')),
                ]);
    return response()->json(['message'=>'New customer added']);
    
   }

   //List all loan application

   public function listRequests(Request $request){
    
     $applications = Loan::with('schedules','user')->orderBy('created_at', 'desc')->get();

     return response()->json($applications);
    
   }

   //Approve Loan
   public function approveLoan(Request $request){
    
       $request->validate([
          'id' => ['required'],
      ]);

     $loan = Loan::where('status',0)
                   ->where('id',$request->id)
                   ->first();

      if($loan){

        $loan->status = 1;

        $loan->save();

      }
      else{

        return response()->json(['message'=>'No application to approve for this id']);

      }

    return response()->json(['message'=>'Loan with reference id '.$loan->loan_id.' is approved']);
    
   }





}

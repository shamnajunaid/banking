<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Loan;
use App\Models\Loan_schedule;
class LoanController extends Controller
{



  //Create new loan application

   public function applyLoan(Request $request){

   	 $request->validate([
        'amount' => ['required'],
        'term_nos' => ['required'],
    ]);

     $term_nos = $request->input('term_nos');

     $amount =  $request->input('amount');

     $loan = new Loan;

     $loan->user_id = $request->user()->id;

     $loan->loan_amount = $amount;

     $loan->term_nos = $term_nos;

     $loan->save();

     //To generate a loan id for reference

     $loan->loan_id = '#'.$loan->id;
     //creating schedule based on applied date

     $date = time();
     
     if($loan->save()){

     
      $term_amount = $amount/$term_nos;

      $term_amount = number_format((float)$term_amount, 3, '.', '');

      //array to store schedules with date

      $data = array();
      $total = 0;
      //Loop to generate schedules array

      for($i=1; $i<=$term_nos; $i++){

        $total +=$term_amount;
//Compare total of terms with actual loan amount to fix the differnce 

        if($i==$term_nos){

          if($total>$amount){

            $term_amount = $term_amount-($total-$amount);

          }
          elseif($total<$amount){

            $term_amount = $term_amount+($amount-$total);

          }

        }
        $date = strtotime("+7 day", $date);

        $data[] =  array('loan_id'=>$loan->id,
                        'due_date'=>date('Y-m-d',$date),
                        'term_amount'=>$term_amount,
                        'status'=>0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                      );
      }

        Loan_schedule::insert($data);

     }

     return response()->json(['message'=>'Your application has been submitted and your refernce loan id is '.$loan->loan_id]);
    
   }

   public function payLoan(Request $request){

    $request->validate([
          'id' => ['required'],
          'amount'=>['required']
      ]);
    $id = $request->id;
    $actual_amount = $amount = $request->amount;

    $loan = Loan::withSum('schedules as total_amount', 'paid_amount')->where('status',1)->where('user_id',$request->user()->id)->find($request->id);

    if(!$loan){
      //if the loan is not aproved or alreay completed its transact
       return response()->json(['message'=>'This  loan is not approved or not created']);
    }
    elseif($loan->total_amount==$loan->loan_amount){
      // if the loan is alreday paid

       return response()->json(['message'=>'Your loan is already completed']);
    }
    elseif(($loan->loan_amount-$loan->total_amount)<$amount){
      return response()->json(['message'=>'Your total due amount is '.($loan->loan_amount-$loan->total_amount).' Please pay a lesser amount than '.$amount]);
    }
    
    while($amount>0){

      $loan_schedule = Loan_schedule::where('loan_id',$id)->where('status',0)->first();

      if(!$loan_schedule){
         break;
      }
      //Calculate paid amount in each schedule

      $payment_amount = $loan_schedule->term_amount-$loan_schedule->paid_amount;

      if($amount>$payment_amount){

          $loan_schedule->paid_amount= $loan_schedule->term_amount;
          $loan_schedule->status = 1;
          $loan_schedule->save();
          //Calculating balnce amount for next schedule
          $amount = $amount-$payment_amount;

      }elseif($amount<$payment_amount){

          $loan_schedule->paid_amount= $loan_schedule->paid_amount+$amount;
          $loan_schedule->save();
          $amount=0;

      }else{

          $loan_schedule->paid_amount= $loan_schedule->paid_amount+$amount;
          $loan_schedule->status = 1;
          $loan_schedule->save();
          $amount=0;
      }
    }
    if($loan->total_amount==$actual_amount){
          $loan->status=2;
          $loan->save();
    }
    //Here the 
    return response()->json(['Payment completed']);
    
   }
public function viewApplications(Request $request){

  $applications = Loan::with('schedules')->where('user_id',$request->user()->id)->orderBy('created_at', 'desc')->get();

     return response()->json($applications);

}

}

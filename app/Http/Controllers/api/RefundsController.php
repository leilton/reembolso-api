<?php

namespace App\Http\Controllers\api;

use App\Refund;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\RefundsRequest;

class RefundsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Refund::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fileupload(Request $request, $id)
    {
        $refund = Refund::find($id);
        if (is_null($refund)) {
            return response()->json([ 'erro' => 'Refound not found' ], 404);
        }

        if($files = $request->file('receipt')){
            $prefix = explode('.', $files->getClientOriginalName());
            $prefix = end($prefix);

            $name = "refound-$id.$prefix";
            $files->move('image',$name);
            $refund->receipt = $name;
            $refund->save();

            return response()->json( $refund, 200 );

        }

        return response()->json([ 'erro' => 'Image not found' ], 404);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [];
        foreach($request->refunds as $refund){
            if(!empty($refund)){
                $refund['date'] = date('Y-m-d H:i:s', strtotime($refund['date']));
                $refund['user_id'] = Auth::id();
                $result[] = $this->saveRefund($refund);
            }
        }

        if(empty($result)){
            return response()->json(['erro' => 'Not refounds'], 422);
        }

        return response()->json(['result' => $result], 201);
    }

    public function saveRefund($refund){
        $validator = Validator::make($refund, [
            'description' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['Messages'=> $validator->errors(), 'refund' => $refund], 401);
        }

        return Refund::create($refund);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Refund::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RefundsRequest $request, $id)
    {
        $refund = Refund::find($id);
        if (is_null($refund)) {
            return response()->json([
                'erro' => 'Refound not found'
            ], 404);
        }
        $refund->value = $request->value;
        $refund->save();

        return $refund;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $refund = Refund::findOrFail($id);
        $refund->delete();

        return response()->json( ['message' => 'Refund remove success' ], 200); 
    }

    public function restore($id)
    {
        $refund = Refund::onlyTrashed()->find($id);
        if (!is_null($refund)) {
            $refund->restore();
            return response()->json($refund, 200);
        }
        return response()->json(['message' => 'refund already restored'], 200);
    }

    public function report (Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        if(!$month || !$year){
            return response()->json(['error' => 'inputs incorrects'], 400);
        }

        $refunds = Refund::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $report = [
            'month' => $month,
            'year' => $year,
            'totalRefunds' => number_format($refunds->sum('value'),'2','.',''),
            'refunds' => count($refunds)
        ];

        return response()->json($report, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $refund = Refund::find($id);
        if (is_null($refund)) {
            return response()->json([
                'erro' => 'Refound not found'
            ], 404);
        }
        $refund->status = $request->status ? false : true;
        $refund->save();

        return $refund;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request, $id)
    {
        $refund = Refund::find($id);
        if (is_null($refund)) {
            return response()->json([
                'erro' => 'Refound not found'
            ], 404);
        }
        $refund->block = $request->block ? false : true;
        $refund->save();

        return $refund;
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Refund;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            return response()->json([
                'erro' => 'Refound not found'
            ], 404);
        }

        if($files = $request->file('receipt')){
            $prefix = explode('.', $files->getClientOriginalName());
            $prefix = end($prefix);

            $name = "refound-$id.$prefix";
            $files->move('image',$name);
            $refund->receipt = $name;
            $refund->save();

            return response()
                ->json(
                    $refund,
                    200
            );

        }

        return response()->json([
            'erro' => 'Image not found'
        ], 404);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usuario = $this->getUser($request->identification);

        $result = [];
        foreach($request->refunds as $refund){
            $refund['date'] = date('Y-m-d H:i:s', strtotime($refund['date']));
            $result[] = Refund::create($refund);
        }

        $result = [
            'name' => $request->name,
            'identification' => $request->identification,
            'jobRole' => $request->jobRole,
            'refunds' => $result,
            'createdAt' => $usuario->created_at
        ];

        return response()
            ->json(
                Refund::create($result),
                201
        );
    }

    function getUser($id){
        $usuario = User::find($id);

        if(is_null($usuario)){
            return response()->json([
                'erro' => 'User not found'
            ], 404);
        }
        return $usuario;
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
    public function update(Request $request, $id)
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
    }

    public function restore($id)
    {
        $refund = Refund::onlyTrashed()->find($id);
        if (!is_null($refund)) {
            $refund->restore();
            return response()->json([
                'erro' => $refund
            ], 200);
        }
        return $refund;
    }

    public function report (Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $refunds = Refund::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalRefunds = $this->totalRefunds($refunds);

        $report = [
            'month' => $month,
            'year' => $year,
            'totalRefunds' => $totalRefunds,
            'refunds' => count($refunds)
        ];

        return $report;
    }

   function totalRefunds ($refunds)
   {
        $totalRefunds = 0;
        foreach ($refunds as $refund)
        {
            $totalRefunds += $refund->value;
        }

        return number_format($totalRefunds,'2','.','');
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

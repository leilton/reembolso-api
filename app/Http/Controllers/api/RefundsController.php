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
    public function store(Request $request)
    {
        $usuario = $this->getUser();

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
        $usuario = User::findOrFail($id);

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
}

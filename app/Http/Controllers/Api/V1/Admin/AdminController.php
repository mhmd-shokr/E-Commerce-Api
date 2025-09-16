<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerRequest;
use App\Models\User;
use App\Notifications\ApproveRequestNotification;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    //get all user request-seller
    public function index(){
    $request=SellerRequest::with('user')->where('status','pending')->get();
    return response()->json($request);
    }

    public function approve($id){
        $request=SellerRequest::findOrFail($id);
        $request->update(["status"=>'approved']);
        $request->user->syncRoles(['Seller']);
        $request->user->notify(new ApproveRequestNotification($request));
    return response()->json(["message"=>'request Approved , User Is Now Seller'],201);
    }

    public function reject(Request $request, $id){
        $request=SellerRequest::findOrFail($id);
        $request->update(["status"=>'rejected']);
        $request->user->notify(new ApproveRequestNotification($request));
    return response()->json(["message"=>'request rejected'],201);
    }


    public function changeRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);
        $user = User::findOrFail($id);
        //mean delete all roles and new roles
        $user->syncRoles([$request->role]);
        return response()->json([
            'message' => 'User role updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first()
            ]
        ], 200);
    }
    

}

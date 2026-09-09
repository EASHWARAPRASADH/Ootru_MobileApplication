<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\DataTables\RoleDataTable;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title',['form' => __('message.role')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        
        $button = '';
        if($auth_user->can('role-add')){
            $button='<a href="'.route('permission.add',['type'=>'role']).'" class="float-right btn btn-sm btn-primary loadRemoteModel"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.role')]).'</a>';
        }
        return $dataTable->render('role.index', compact('assets','pageTitle','button','auth_user'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.role')]);

        if ($role != '') {
            $protected_roles = ['admin', 'demo_admin', 'client', 'delivery_man', 'user', 'agent'];
            if (in_array(strtolower($role->name), $protected_roles)) {
                $message = __('message.demo_permission_denied');
                if (request()->ajax()) {
                    return response()->json(['status' => false, 'message' => 'System roles cannot be deleted.']);
                }
                return redirect()->back()->withErrors('System roles cannot be deleted.');
            }

            $usersCount = \DB::table('model_has_roles')->where('role_id', $role->id)->count();
            if ($usersCount > 0) {
                $message = "Cannot delete role because {$usersCount} user(s) are currently assigned to it. Please reassign the user(s) first.";
                if (request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message]);
                }
                return redirect()->back()->withErrors($message);
            }

            $role->delete();
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.role')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}

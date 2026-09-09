<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;

class PermissionController extends Controller
{
    public function __construct()
    {
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sidebarOrder = [
            'dashboard',
            'country',
            'city',
            'order',
            'users',
            'subadmin',
            'deliveryman',
            'deliverymandocument',
            'document',
            'vehicle',
            'extracharge',
            'parcel_type',
            'staticdata',
            'couriercompanies',
            'push notification',
            'report',
            'mail_template',
            'ordermail',
            'sms_template',
            'ordersms',
            'withdrawrequest',
            'claims',
            'customersupport',
            'coupon',
            'emergency',
            'app_language_setting',
            'role',
            'permission',
            'website_section',
            'pages',
            'setting',
            'rest_api',
        ];

        $permission = Permission::whereNull('parent_id')
            ->with('subpermission')
            ->get()
            ->sortBy(function ($item) use ($sidebarOrder) {
                $pos = array_search(strtolower($item->name), $sidebarOrder);
                return $pos === false ? 999 : $pos;
            })
            ->values();

        $pageTitle = __('message.list_form_title',['form' => __('message.permission')  ]);

        $authUser = \Auth::user();
        if ($authUser && !$authUser->hasRole('admin')) {
            $userPermissions = $authUser->getAllPermissions()->pluck('name')->toArray();
            $permission = $permission->filter(function ($parent) use ($userPermissions) {
                if ($parent->subpermission && $parent->subpermission->count() > 0) {
                    $matchingSubs = $parent->subpermission->filter(function ($sub) use ($userPermissions) {
                        return in_array($sub->name, $userPermissions);
                    });
                    $parent->setRelation('subpermission', $matchingSubs);
                    return $matchingSubs->count() > 0;
                }
                return in_array($parent->name, $userPermissions);
            })->values();

            $roles = Role::where('status', 1)
                ->whereNotIn('name', ['admin', 'client', 'delivery_man'])
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            $roles = Role::where('status', 1)->orderBy('name', 'ASC')->get();
        }

        $auth_user = authSession();

        return view('permission.index',compact(['roles','permission','pageTitle','auth_user']));
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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $authUser = \Auth::user();
        $isAdmin = $authUser && $authUser->hasRole('admin');
        $data = isset($request->permission) ? $request->permission : [];

        if (!$isAdmin) {
            // Sub-admin: can only manage permissions they themselves possess
            $userPermissions = $authUser ? $authUser->getAllPermissions()->pluck('name')->toArray() : [];
            $permission_list = Permission::whereIn('name', $userPermissions)->get()->unique('name');
            $managedRoles = Role::whereNotIn('name', ['admin', 'client', 'delivery_man'])->get();
            $managedRoleNames = $managedRoles->pluck('name')->toArray();

            foreach ($managedRoles as $role) {
                $role->revokePermissionTo($permission_list);
            }

            // Filter incoming submitted permissions to allowed subset
            $filteredData = [];
            foreach ($data as $key => $roleArray) {
                if (in_array($key, $userPermissions)) {
                    $validRoles = array_intersect($roleArray, $managedRoleNames);
                    if (!empty($validRoles)) {
                        $filteredData[$key] = $validRoles;
                    }
                }
            }
            $data = $filteredData;
        } else {
            // Super Admin: full access
            $permission_list = Permission::orderBy('name','ASC')->get()->unique('name');
            Role::whereNotIn('name',['admin'])->get()->map(function($role) use($permission_list){
                $role->revokePermissionTo($permission_list);
            });
        }

        if(count($data)>0){
            foreach ($data as $key => $permissionRoles){
                foreach ($permissionRoles as $role){
                    $perm = Permission::findOrCreate($key);
                    $guard = Role::findOrCreate($role,'web');
                    $guard->givePermissionTo($perm);
                }
            }
        }
        Artisan::call('permission:cache-reset');
        return redirect()->route('permission.index')->withSuccess(__('message.save_form',['form' => __('message.permission')]));
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
        //
    }

    public function addPermission($type){
        switch ($type){
            case 'permission' :
                $title = __('message.add_form_title',['form' => __('message.permission')  ]);
                break;
            case 'role' :
                $title = __('message.add_form_title',['form' => __('message.role')  ]);
                break;
            default :
                $title = __('message.add_form_title',['form' => __('message.permission')  ]);
                break;
        }
        return view('permission.add_permission',compact(['title','type']));
    }

    public function savePermission(Request $request)
    {
        $data = $request->all();

        switch ($data['type']){
            case 'permission' :
                    $validator = \Validator::make($data,[
                        'name' => 'required|max:191|unique:permissions,name',
                    ]);

                    if($validator->fails()) {
                        $message = $validator->errors()->first();
                        return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                    }
                    $permission = Permission::create([
                        'name' => $data['name'],
                        'parent_id' => isset($request->parent_id) ? $request->parent_id : null,
                    ]);
                    $admin_role = Role::findByName('admin');
                    $admin_role->givePermissionTo($permission);
                    break;
            case 'role' :
                    $validator = \Validator::make($data,[
                        'name' => 'required|max:191|regex:/^[\pL\s\-]+$/u|unique:roles,name',
                    ]);

                    if($validator->fails()) {
                        $message = $validator->errors()->first();
                        return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                    }

                    $admin_role = Role::create([
                        'name' => $data['name'] ,
                        'status' => '1'
                    ]);
                    break;
            default :
                    return response()->json(['status'=>false,'event' => 'validation' , 'message' => 'Try Again']);
                    break;
        }
        $message = __('message.save_form',['name'=> __('message.'.$data['type']) ] );

        return response()->json(['status' => true,'event' => 'refresh' , 'message' => $message]);
    }
}

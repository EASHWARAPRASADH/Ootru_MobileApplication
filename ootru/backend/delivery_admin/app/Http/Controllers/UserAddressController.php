<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $userAddress = UserAddress::where('user_id', $user->id)
        ->where(function($query) use ($user,$request){
            $query->where('city_id',$user->city_id)
               ->orWhere('city_id',$request->city_id);
        })
        ->where(function ($query) use ($user, $request) {
            $query->where('country_id', $user->country_id)
                ->orWhere('country_id', $request->country_id);
        });


        $pageTitle = __('message.my_address');

        if ($request->filled('city_id')) {
            $userAddress->whereHas('city', function ($query) use ($request) {
                $query->where('id', $request->input('city_id'));
            });
        }
        if ($request->filled('country_id')) {
            $userAddress->whereHas('country', function ($query) use ($request) {
                $query->where('id', $request->input('country_id'));
            });
        }

        $userAddresses = $userAddress->get();

        $cityId = $user->city_id;
        $countryId = $user->country_id;

        if ($request->filled('city_id')) {
            $cityId = $request->input('city_id');
        }

        if ($request->filled('country_id')) {
            $countryId = $request->input('country_id');
        }
        if ($cityId != null && $countryId != null) {
            $selectedCity = City::where('id', $cityId)->pluck('name', 'id');
            $selectedCountry = Country::where('id', $countryId)->pluck('name', 'id');
        } else {
            $selectedCity = City::pluck('name', 'id')->prepend('Select City', '');
            $selectedCountry = Country::pluck('name', 'id')->prepend('Select Country', '');
        }

        return view('clientside.addressmain', compact('pageTitle', 'userAddresses', 'selectedCity', 'selectedCountry'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $assets = ['phone','location'];
        $user = Auth::user()->id;
        $userdata = User::find($user);
        $pageTitle = __('message.add_form_title', ['form' => __('message.address')]);
        return view('clientside.address',compact('userdata','assets','pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $result = UserAddress::updateOrCreate(['id' => $request->id], $data);


        $message = __('message.update_form',[ 'form' => __('message.useraddress') ] );
		if($result->wasRecentlyCreated){
			$message = __('message.save_form',[ 'form' => __('message.useraddress') ] );
		}

        if($request->is('api/*')) {
            return json_message_response($message);
		}
        return redirect()->route('useraddress.index')->withSuccess($message);

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
        $useraddress = UserAddress::find($id);
        $message = __('message.msg_fail_to_delete',['item' => __('message.useraddress')] );

        if($useraddress != '') {
            $useraddress->delete();
            $message = __('message.msg_deleted',['name' => __('message.useraddress')] );
        }
        if(request()->is('api/*')){
            return json_custom_response(['message'=> $message , 'status' => true]);
        }
    }
}

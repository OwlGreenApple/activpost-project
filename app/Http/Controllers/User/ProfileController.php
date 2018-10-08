<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request as req;

use Celebpost\User;

class ProfileController extends Controller
{
    public function update(req $request)
    {
	    $this->validate($request, [
	        // 'username' => 'required|max:255|unique:users,username,'.$request->user()->id,
            // 'name' => 'required|max:255',
            // 'email' => 'required|email|max:255|unique:users,email,'.$request->user()->id,
            'password' => 'min:6|confirmed',
	    ]);
	    $user = User::findOrFail($request->user()->id);
	    if ($request->has('password')) {
	    	$user->password = bcrypt($request->password);
	    }
	    $user->save();
    	return back()->with('status', 'Password Berhasil diubah');
    }
}

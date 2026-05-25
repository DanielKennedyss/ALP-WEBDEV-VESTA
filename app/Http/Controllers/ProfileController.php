<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        // Redirect admin/staff to their dashboard
        if (in_array($user->role, ['owner', 'manager', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update user details (Name, Email, Phone Number).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($request->only('name', 'email', 'phone_number'));

        // If email changed, invalidate verification to match Breeze/test expectations
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile details updated successfully.');
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify current password manually for high compatibility and specific error bag support
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.'
            ], 'updatePassword');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Delete user account.
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'string'],
        ]);

        // Verify password before deletion
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password does not match your current password.'
            ], 'userDeletion');
        }

        // Log out the user first
        Auth::logout();

        // Delete the user record
        $user->delete();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/')->with('success', 'Your account has been deleted successfully.');
    }
}

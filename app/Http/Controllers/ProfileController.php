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

        $emailChanged = $request->email !== $user->email;

        if ($emailChanged) {
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            session(['change_email_pending' => $request->email]);

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ChangeEmailOtpMail($otpCode, $user->name));

            // Save other profile details immediately
            $user->fill($request->only('name', 'phone_number'));
            $user->save();

            return redirect()->route('profile.change-email.verify.form')->with('success', 'An OTP code has been sent to your current email address to verify this change.');
        }

        // Save other profile details immediately
        $user->fill($request->only('name', 'phone_number'));
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

    /**
     * Store a new address for the user.
     */
    public function storeAddress(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'province_name' => ['required', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'full_address' => ['required', 'string'],
        ]);

        // Case-insensitive duplicate check
        $labelExists = $user->addresses()
            ->whereRaw('LOWER(label) = ?', [strtolower(trim($request->label))])
            ->exists();

        if ($labelExists) {
            return back()->withInput()->with([
                'error' => 'An address with this label already exists.',
                'open-addresses-tab' => true,
            ]);
        }

        $isDefault = $request->has('is_default');

        // If this is the first address or marked as default, unset other defaults
        if ($isDefault || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_default' => false]);
            $isDefault = true;
        }

        $user->addresses()->create([
            'label' => $request->label,
            'province_name' => $request->province_name,
            'city_name' => $request->city_name,
            'full_address' => $request->full_address,
            'is_default' => $isDefault,
        ]);

        return redirect()->route('profile')->with([
            'success' => 'Address added successfully.',
            'open-addresses-tab' => true,
        ]);
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(\Illuminate\Http\Request $request, \App\Models\Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'province_name' => ['required', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'full_address' => ['required', 'string'],
        ]);

        // Case-insensitive duplicate check excluding current address ID
        $labelExists = Auth::user()->addresses()
            ->where('id', '!=', $address->id)
            ->whereRaw('LOWER(label) = ?', [strtolower(trim($request->label))])
            ->exists();

        if ($labelExists) {
            return back()->withInput()->with([
                'error' => 'An address with this label already exists.',
                'open-addresses-tab' => true,
            ]);
        }

        $isDefault = $request->has('is_default');

        if ($isDefault) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update([
            'label' => $request->label,
            'province_name' => $request->province_name,
            'city_name' => $request->city_name,
            'full_address' => $request->full_address,
            'is_default' => $isDefault,
        ]);

        return redirect()->route('profile')->with([
            'success' => 'Address updated successfully.',
            'open-addresses-tab' => true,
        ]);
    }

    /**
     * Delete an address.
     */
    public function destroyAddress(\App\Models\Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If we deleted the default address, set another one as default if exists
        if ($wasDefault) {
            $next = Auth::user()->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return redirect()->route('profile')->with([
            'success' => 'Address deleted successfully.',
            'open-addresses-tab' => true,
        ]);
    }

    /**
     * Show the Change Email OTP verification form.
     */
    public function showChangeEmailVerifyForm()
    {
        if (!session('change_email_pending')) {
            return redirect()->route('profile');
        }
        return view('profile.change-email-verify');
    }

    /**
     * Verify the Change Email OTP.
     */
    public function verifyChangeEmailOtp(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $pendingEmail = session('change_email_pending');
        if (!$pendingEmail) {
            return redirect()->route('profile')->with('error', 'Session expired. Please try updating your email again.');
        }

        $user = Auth::user();

        // Check OTP validity
        if ($user->otp_code !== $request->otp) {
            return back()->with('error', 'The OTP code you entered is incorrect.');
        }

        // Check OTP expiration
        if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);
            return back()->with('error', 'This OTP has expired. Please request a new one.');
        }

        // OTP is correct and valid. Update the email.
        $user->update([
            'email' => $pendingEmail,
            'email_verified_at' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('change_email_pending');

        return redirect()->route('profile')->with('success', 'Your email address has been updated successfully.');
    }

    /**
     * Resend the Change Email OTP.
     */
    public function resendChangeEmailOtp(\Illuminate\Http\Request $request)
    {
        $pendingEmail = session('change_email_pending');
        if (!$pendingEmail) {
            return redirect()->route('profile')->with('error', 'Session expired. Please try updating your email again.');
        }

        $user = Auth::user();
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ChangeEmailOtpMail($otpCode, $user->name));

        return back()->with('success', 'A new OTP has been sent to your current email address.');
    }
}

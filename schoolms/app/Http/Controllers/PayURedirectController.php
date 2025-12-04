<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Brian2694\Toastr\Facades\Toastr;

class PayURedirectController extends Controller
{
    /**
     * Display a redirect page that auto-submits the PayU payment form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        $formData = Session::get('payu_form_data');
        $actionUrl = Session::get('payu_action_url');

        if (!$formData || !$actionUrl) {
            Toastr::error('Payment session expired. Please try again.', 'Error');
            return redirect()->back();
        }

        return view('payment.payu_redirect', [
            'formData' => $formData,
            'actionUrl' => $actionUrl,
        ]);
    }
}

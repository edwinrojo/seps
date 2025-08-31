<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvitationEmail;

class EmailController extends Controller
{
    public function sendInvitationEmail()
    {
        $data = [
            'name' => 'John Doe',
            'message' => 'This is a test email from Laravel 12.'
        ];

        Mail::to('recipient@example.com')->send(new SendInvitationEmail($data));

        return response()->json(['success' => 'Email sent successfully.']);
    }
}

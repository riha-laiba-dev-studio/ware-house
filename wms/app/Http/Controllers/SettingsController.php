<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'mail_mailer'     => env('MAIL_MAILER', 'log'),
            'mail_host'       => env('MAIL_HOST', ''),
            'mail_port'       => env('MAIL_PORT', '587'),
            'mail_username'   => env('MAIL_USERNAME', ''),
            'mail_from'       => env('MAIL_FROM_ADDRESS', 'wms@example.com'),
            'mail_from_name'  => env('MAIL_FROM_NAME', 'WMS Pro'),
            'twilio_sid'      => env('TWILIO_SID', ''),
            'twilio_from'     => env('TWILIO_FROM', ''),
            'sms_enabled'     => env('SMS_ENABLED', 'false'),
            'currency'        => env('WMS_CURRENCY', 'PKR'),
            'company_name'    => env('WMS_COMPANY', 'WMS Pro'),
            'company_email'   => env('WMS_EMAIL', 'admin@wms.com'),
            'company_phone'   => env('WMS_PHONE', ''),
            'company_address' => env('WMS_ADDRESS', ''),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mail_host'      => 'nullable|string',
            'mail_port'      => 'nullable|numeric',
            'mail_username'  => 'nullable|string',
            'mail_password'  => 'nullable|string',
            'mail_from'      => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'currency'       => 'nullable|string|max:10',
            'company_name'   => 'nullable|string|max:100',
            'company_email'  => 'nullable|email',
            'company_phone'  => 'nullable|string|max:30',
            'company_address' => 'nullable|string|max:255',
            'twilio_sid'     => 'nullable|string',
            'twilio_token'   => 'nullable|string',
            'twilio_from'    => 'nullable|string',
            'sms_enabled'    => 'nullable|string',
        ]);

        // ✅ FIX 1: define env path
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return back()->with('error', '.env file not found');
        }

        $envContent = file_get_contents($envPath);

        $replacements = [
            'MAIL_HOST'         => $data['mail_host'] ?? '',
            'MAIL_PORT'         => $data['mail_port'] ?? '587',
            'MAIL_USERNAME'     => $data['mail_username'] ?? '',
            'MAIL_FROM_ADDRESS' => $data['mail_from'] ?? 'wms@example.com',
            'MAIL_FROM_NAME'    => '"' . ($data['mail_from_name'] ?? 'WMS Pro') . '"',
            'WMS_CURRENCY'      => $data['currency'] ?? 'PKR',
            'WMS_COMPANY'       => '"' . ($data['company_name'] ?? 'WMS Pro') . '"',
            'WMS_EMAIL'         => $data['company_email'] ?? '',
            'WMS_PHONE'         => $data['company_phone'] ?? '',
            'WMS_ADDRESS'       => '"' . ($data['company_address'] ?? '') . '"',
            'SMS_ENABLED'       => $data['sms_enabled'] ?? 'false',
        ];

        if (!empty($data['mail_password'])) {
            $replacements['MAIL_PASSWORD'] = $data['mail_password'];
        }
        if (!empty($data['twilio_sid'])) {
            $replacements['TWILIO_SID'] = $data['twilio_sid'];
        }
        if (!empty($data['twilio_token'])) {
            $replacements['TWILIO_TOKEN'] = $data['twilio_token'];
        }
        if (!empty($data['twilio_from'])) {
            $replacements['TWILIO_FROM'] = $data['twilio_from'];
        }

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        Artisan::call('config:clear');

        // ✅ FIX 2: safe redirect (you already corrected this part 👍)
        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            Mail::raw('This is a test email from WMS Pro. Your email configuration is working correctly!', function ($m) use ($request) {
                $m->to($request->email)->subject('WMS Pro — Test Email');
            });
            return response()->json(['message' => 'Test email sent successfully to ' . $request->email]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }
}

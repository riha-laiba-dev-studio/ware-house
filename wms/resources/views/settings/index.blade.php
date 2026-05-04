@extends('layouts.app')
@section('title','Settings')
@section('page-title','System Settings')

@section('content')
<form method="POST" action="{{ route('settings.update') }}">
@csrf
@method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- Sidebar Nav --}}
  <div class="space-y-2">
    <div class="card p-2">
      <button type="button" onclick="switchTab('company')" class="settings-tab active w-full" id="tab-company">
        <i class="fas fa-building w-5"></i> Company Info
      </button>
      <button type="button" onclick="switchTab('email')" class="settings-tab w-full" id="tab-email">
        <i class="fas fa-envelope w-5"></i> Email / SMTP
      </button>
      <button type="button" onclick="switchTab('sms')" class="settings-tab w-full" id="tab-sms">
        <i class="fas fa-mobile w-5"></i> SMS (Twilio)
      </button>
    </div>
    <button type="submit" class="btn-primary w-full justify-center py-3">
      <i class="fas fa-save"></i> Save Settings
    </button>
    <a href="{{ route('notifications.index') }}" class="btn-outline w-full justify-center">
      <i class="fas fa-bell"></i> View Alerts
    </a>
  </div>

  {{-- Settings Panels --}}
  <div class="lg:col-span-2 space-y-6">

    {{-- Company Info --}}
    <div id="panel-company" class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700"><i class="fas fa-building text-blue-500 mr-2"></i>Company Information</h3>
        <span class="badge badge-info">General</span>
      </div>
      <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="form-label">Company Name</label>
          <input type="text" name="company_name" class="form-input" value="{{ $settings['company_name'] }}" placeholder="WMS Pro">
        </div>
        <div>
          <label class="form-label">Company Email</label>
          <input type="email" name="company_email" class="form-input" value="{{ $settings['company_email'] }}" placeholder="admin@company.com">
        </div>
        <div>
          <label class="form-label">Company Phone</label>
          <input type="text" name="company_phone" class="form-input" value="{{ $settings['company_phone'] }}" placeholder="+92 300 0000000">
        </div>
        <div>
          <label class="form-label">Currency Code</label>
          <input type="text" name="currency" class="form-input" value="{{ $settings['currency'] }}" placeholder="PKR" maxlength="10">
          <p class="text-xs text-gray-400 mt-1">Used on invoices and reports (e.g. PKR, USD, EUR)</p>
        </div>
        <div class="md:col-span-2">
          <label class="form-label">Company Address</label>
          <textarea name="company_address" class="form-input" rows="2" placeholder="123 Business Street, City, Country">{{ $settings['company_address'] }}</textarea>
        </div>
      </div>
    </div>

    {{-- Email / SMTP --}}
    <div id="panel-email" class="card hidden">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700"><i class="fas fa-envelope text-blue-500 mr-2"></i>Email / SMTP Configuration</h3>
        <span class="badge {{ $settings['mail_mailer'] === 'log' ? 'badge-warning' : 'badge-success' }}">
          {{ $settings['mail_mailer'] === 'log' ? 'Log Mode (dev)' : 'SMTP Active' }}
        </span>
      </div>
      <div class="card-body">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5 text-sm text-blue-800">
          <i class="fas fa-info-circle mr-2"></i>
          Configure SMTP to send real email alerts. Without SMTP, emails are written to <code class="bg-blue-100 px-1 rounded">storage/logs/laravel.log</code>.
          Common providers: Gmail (smtp.gmail.com:587), Mailgun, SendGrid.
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">SMTP Host</label>
            <input type="text" name="mail_host" class="form-input" value="{{ $settings['mail_host'] }}" placeholder="smtp.gmail.com">
          </div>
          <div>
            <label class="form-label">SMTP Port</label>
            <input type="number" name="mail_port" class="form-input" value="{{ $settings['mail_port'] }}" placeholder="587">
          </div>
          <div>
            <label class="form-label">Username / Email</label>
            <input type="text" name="mail_username" class="form-input" value="{{ $settings['mail_username'] }}" placeholder="you@gmail.com">
          </div>
          <div>
            <label class="form-label">Password / App Password</label>
            <input type="password" name="mail_password" class="form-input" placeholder="Leave blank to keep current">
            <p class="text-xs text-gray-400 mt-1">For Gmail: use an App Password, not your regular password</p>
          </div>
          <div>
            <label class="form-label">From Address</label>
            <input type="email" name="mail_from" class="form-input" value="{{ $settings['mail_from'] }}" placeholder="wms@company.com">
          </div>
          <div>
            <label class="form-label">From Name</label>
            <input type="text" name="mail_from_name" class="form-input" value="{{ $settings['mail_from_name'] }}" placeholder="WMS Pro">
          </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100">
          <p class="text-sm font-medium text-gray-700 mb-3">Test Email</p>
          <div class="flex gap-3">
            <input type="email" id="testEmailAddr" class="form-input flex-1" placeholder="Enter email to test...">
            <button type="button" onclick="sendTestEmail()" class="btn-outline whitespace-nowrap">
              <i class="fas fa-paper-plane"></i> Send Test
            </button>
          </div>
          <div id="testEmailResult" class="mt-2 text-sm hidden"></div>
        </div>
      </div>
    </div>

    {{-- SMS / Twilio --}}
    <div id="panel-sms" class="card hidden">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700"><i class="fas fa-mobile text-blue-500 mr-2"></i>SMS Notifications (Twilio)</h3>
        <span class="badge {{ $settings['sms_enabled'] === 'true' ? 'badge-success' : 'badge-gray' }}">
          {{ $settings['sms_enabled'] === 'true' ? 'Enabled' : 'Disabled' }}
        </span>
      </div>
      <div class="card-body">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-sm text-amber-800">
          <i class="fas fa-info-circle mr-2"></i>
          SMS alerts require a <strong>Twilio</strong> account. Sign up at <strong>twilio.com</strong>, get your Account SID, Auth Token, and a phone number.
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2 flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <input type="checkbox" name="sms_enabled" value="true" id="smsToggle" {{ $settings['sms_enabled']==='true'?'checked':'' }} class="w-4 h-4 rounded text-blue-600">
            <label for="smsToggle" class="text-sm font-medium text-gray-700">Enable SMS Notifications</label>
          </div>
          <div>
            <label class="form-label">Twilio Account SID</label>
            <input type="text" name="twilio_sid" class="form-input font-mono" value="{{ $settings['twilio_sid'] }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
          </div>
          <div>
            <label class="form-label">Twilio Auth Token</label>
            <input type="password" name="twilio_token" class="form-input" placeholder="Leave blank to keep current">
          </div>
          <div>
            <label class="form-label">Twilio Phone Number (From)</label>
            <input type="text" name="twilio_from" class="form-input" value="{{ $settings['twilio_from'] }}" placeholder="+12345678900">
          </div>
        </div>
        <div class="mt-5 pt-4 border-t border-gray-100">
          <p class="text-xs text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            SMS alerts will be sent for: low stock items, overdue payments. Alerts trigger when generating from the Alerts &amp; Notifications page.
          </p>
        </div>
      </div>
    </div>

  </div>
</div>
</form>

@push('scripts')
<script>
function switchTab(tab) {
  ['company','email','sms'].forEach(t => {
    document.getElementById('panel-'+t).classList.add('hidden');
    document.getElementById('tab-'+t).classList.remove('active');
  });
  document.getElementById('panel-'+tab).classList.remove('hidden');
  document.getElementById('tab-'+tab).classList.add('active');
}

function sendTestEmail() {
  const email = document.getElementById('testEmailAddr').value;
  const result = document.getElementById('testEmailResult');
  if (!email) { result.textContent = 'Please enter an email address.'; result.className = 'mt-2 text-sm text-red-600'; result.classList.remove('hidden'); return; }
  result.textContent = 'Sending...'; result.className = 'mt-2 text-sm text-blue-600'; result.classList.remove('hidden');
  $.post('{{ route("settings.test-email") }}', { email, _token: $('meta[name="csrf-token"]').attr('content') })
    .done(r => { result.textContent = r.message; result.className = 'mt-2 text-sm text-emerald-600'; })
    .fail(e => { result.textContent = 'Error: ' + (e.responseJSON?.message || 'Failed to send'); result.className = 'mt-2 text-sm text-red-600'; });
}
</script>
@endpush

@section('extra-css')
<style>
.settings-tab { @apply flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all text-left cursor-pointer; }
.settings-tab.active { @apply bg-blue-600 text-white; }
</style>
@endsection
@endsection

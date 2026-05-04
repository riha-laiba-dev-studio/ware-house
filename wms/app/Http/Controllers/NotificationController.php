<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use App\Mail\LowStockAlert;
use App\Mail\PaymentDueAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function index()
    {
        $lowStockItems = Inventory::with(['item.category','item.unit','warehouse'])
            ->whereRaw('inventory.quantity <= (SELECT alert_quantity FROM items WHERE items.id = inventory.item_id)')
            ->join('items','items.id','=','inventory.item_id')
            ->select('inventory.*')
            ->orderByRaw('inventory.quantity / items.alert_quantity ASC')
            ->get();

        $overduePayablesSales = Sale::with(['customer'])
            ->where('payment_status','!=','paid')
            ->where('due_amount','>',0)
            ->where('status','confirmed')
            ->orderByDesc('due_amount')
            ->get();

        $overduePayablesPurchases = Purchase::with(['supplier'])
            ->where('payment_status','!=','paid')
            ->where('due_amount','>',0)
            ->orderByDesc('due_amount')
            ->get();

        $totalLowStock = $lowStockItems->count();
        $totalSalesDue = $overduePayablesSales->sum('due_amount');
        $totalPurchaseDue = $overduePayablesPurchases->sum('due_amount');

        return view('notifications.index', compact(
            'lowStockItems','overduePayablesSales','overduePayablesPurchases',
            'totalLowStock','totalSalesDue','totalPurchaseDue'
        ));
    }

    public function sendLowStockEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $lowStockItems = Inventory::with(['item.category','item.unit','warehouse'])
            ->whereRaw('inventory.quantity <= (SELECT alert_quantity FROM items WHERE items.id = inventory.item_id)')
            ->join('items','items.id','=','inventory.item_id')
            ->select('inventory.*')
            ->get();

        if ($lowStockItems->isEmpty()) {
            return back()->with('info', 'No low stock items found — no email sent.');
        }

        Mail::to($request->email)->send(new LowStockAlert($lowStockItems));
        return back()->with('success', "Low stock alert sent to {$request->email} ({$lowStockItems->count()} items).");
    }

    public function sendPaymentDueEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type'  => 'required|in:sales,purchases',
        ]);

        if ($request->type === 'sales') {
            $records = Sale::with('customer')->where('payment_status','!=','paid')->where('due_amount','>',0)->where('status','confirmed')->get();
            $label = 'Sales';
        } else {
            $records = Purchase::with('supplier')->where('payment_status','!=','paid')->where('due_amount','>',0)->get();
            $label = 'Purchases';
        }

        if ($records->isEmpty()) {
            return back()->with('info', "No outstanding {$label} dues found.");
        }

        Mail::to($request->email)->send(new PaymentDueAlert($records, $request->type));
        return back()->with('success', "Payment due alert ({$label}) sent to {$request->email} ({$records->count()} records).");
    }
}

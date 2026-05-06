<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category','unit','brand'])->withSum('inventory','quantity');
        if ($request->search) $query->where(fn($q) => $q->where('name','like','%'.$request->search.'%')->orWhere('sku','like','%'.$request->search.'%'));
        if ($request->category_id) $query->where('category_id',$request->category_id);
        if ($request->brand_id)    $query->where('brand_id',$request->brand_id);
        $items = $query->paginate(15)->withQueryString();
        $categories = Category::active()->get();
        $brands     = Brand::active()->get();
        return view('items.index', compact('items','categories','brands'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $units      = Unit::active()->get();
        $brands     = Brand::active()->get();
        return view('items.create', compact('categories','units','brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'nullable|string|max:100|unique:items',
            'barcode'        => 'nullable|string|max:100|unique:items',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'required|exists:units,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'description'    => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'min_selling_price' => 'nullable|numeric|min:0',
            'alert_quantity' => 'integer|min:0',
            'image'          => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items','public');
        }
        Item::create($data);
        return redirect()->route('items.index')->with('success','Item created successfully.');
    }

    public function show(Item $item)
    {
        $item->load(['category','unit','brand','variants','inventory.warehouse']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::active()->get();
        $units      = Unit::active()->get();
        $brands     = Brand::active()->get();
        return view('items.edit', compact('item','categories','units','brands'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'nullable|string|max:100|unique:items,sku,'.$item->id,
            'barcode'        => 'nullable|string|max:100|unique:items,barcode,'.$item->id,
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'required|exists:units,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'description'    => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'min_selling_price' => 'nullable|numeric|min:0',
            'alert_quantity' => 'integer|min:0',
            'is_active'      => 'boolean',
            'image'          => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items','public');
        }
        $item->update($data);
        return redirect()->route('items.index')->with('success','Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success','Item deleted.');
    }
}

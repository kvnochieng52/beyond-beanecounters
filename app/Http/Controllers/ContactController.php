<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{



    public function index()
    {
        $contacts = Contact::with('contactLists')->paginate(10);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|integer',
            'contact_lists.*.name' => 'required|string|max:255',
            'contact_lists.*.telephone' => 'required|string|max:15',
        ]);

        $contact = Contact::create($request->only('title', 'is_active', 'created_by', 'updated_by'));

        foreach ($request->contact_lists as $list) {
            $contact->contactLists()->create($list);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load('contactLists');
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $contact->load('contactLists');
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|integer',
            'contact_lists.*.name' => 'required|string|max:255',
            'contact_lists.*.telephone' => 'required|string|max:15',
        ]);

        $contact->update($request->only('title', 'is_active', 'updated_by'));
        $contact->contactLists()->delete();

        foreach ($request->contact_lists as $list) {
            $contact->contactLists()->create($list);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->contactLists()->delete();
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }


    public function getContacts(Request $request)
    {
        //     $contactIds = $request->contact_ids;

        //     if (is_string($contactIds)) {
        //         $contactIds = json_decode($contactIds, true);
        //     }

        //     if (!$contactIds || !is_array($contactIds) || empty($contactIds)) {
        //         return response()->json(['success' => false, 'message' => 'No contacts found.', 'contacts' => $contactIds]);
        //     }

        //     $contacts = Contact::whereIn('id', $contactIds)->get(['id', 'title']);


        //     $contactTitle = optional($contacts->first())->title ?? 'Unknown';

        //     return response()->json([
        //         'success' => true,
        //         'contacts' => $contacts,
        //         'contact_title' => $contactTitle
        //     ]);
        // }


        $contactIds = $request->contact_ids;

        // Ensure $contactIds is an array
        if (is_string($contactIds)) {
            $contactIds = json_decode($contactIds, true);
        }

        if (!$contactIds || !is_array($contactIds) || empty($contactIds)) {
            return response()->json(['success' => false, 'message' => 'No contacts found.']);
        }

        $contacts = Contact::whereIn('contacts.id', $contactIds)
            ->leftJoin('contact_lists', 'contacts.id', '=', 'contact_lists.contact_id')
            ->select('contacts.id', 'contacts.title', DB::raw('COUNT(contact_lists.id) as contact_list_count'))
            ->groupBy('contacts.id', 'contacts.title')
            ->get();

        return response()->json([
            'success' => true,
            'contacts' => $contacts
        ]);
    }
}

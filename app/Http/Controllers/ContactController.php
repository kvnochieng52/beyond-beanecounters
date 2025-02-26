<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\Request;

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
}

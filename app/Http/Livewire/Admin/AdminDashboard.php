<?php

namespace App\Http\Livewire\Admin;

use App\Http\Livewire\Livewire;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\EmployeeInfo;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AdminDashboard extends Component
{
    protected $layout = 'layouts.admin.app';

    public function render()
    {
        // Make sure this path is correct
        return view('modules.admin.dashboard.index')->layout('layouts.admin.app');
        // NOT: view('livewire.app.http.livewire.admin.admin-dashboard')
    }
}
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Models\Plan;
use App\Models\User;
use App\Models\Payment;

class AdminController extends Controller
{
    public function stats()
    {
        $totalUsers = count(User::all());
        $totalPayments = count(Payment::where('status', 'paid'));
        $totalRevenue = array_sum(array_column(Payment::where('status', 'paid'), 'amount'));
        $this->view('admin/stats', [
            'totalUsers' => $totalUsers,
            'totalPayments' => $totalPayments,
            'totalRevenue' => $totalRevenue,
            'title' => 'Thống kê'
        ]);
    }

    public function users()
    {
        $users = User::all();
        foreach ($users as &$user) {
            $user['subscription'] = \App\Models\Subscription::getActiveByUserId($user['id']);
        }
        $this->view('admin/users', ['users' => $users, 'title' => 'Quản lý Người dùng']);
    }

    public function plans()
    {
        $plans = Plan::all();
        $this->view('admin/plans', ['plans' => $plans, 'title' => 'Quản lý Gói']);
    }

    public function updatePlan()
    {
        $id = $_POST['id'];
        $data = [
            'name' => trim($_POST['name']),
            'max_pages' => (int)$_POST['max_pages'],
            'price' => (float)$_POST['price']
        ];
        if (Plan::update($id, $data)) {
            set_flash('success', 'Cập nhật gói thành công.');
        } else {
            set_flash('error', 'Cập nhật gói thất bại.');
        }
        Response::redirect('/admin/plans');
    }

    public function blockUser($id)
    {
        if (User::update($id, ['status' => 'blocked'])) {
            set_flash('success', 'Khóa người dùng thành công.');
        } else {
            set_flash('error', 'Khóa người dùng thất bại.');
        }
        Response::redirect('/admin/users');
    }
}

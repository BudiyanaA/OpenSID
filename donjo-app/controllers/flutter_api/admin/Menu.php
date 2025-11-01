<?php

// use App\Models\Wilayah as WilayahModel;

defined('BASEPATH') || exit('No direct script access allowed');

use App\Models\Menu as MenuModel;

class Menu extends MY_Controller
{
    public function index()
    {
        $parent    = (int) ($this->input->get('parent') ?? 0);
        $status    = $this->input->get('status') ?? null;

        $data   = MenuModel::child($parent)
          ->with(['parent'])
          ->orderBy('urut', 'asc')
          ->when(in_array($status, ['0', '1']), static fn ($q) => $q->where('enabled', $status))
          ->get();

        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}
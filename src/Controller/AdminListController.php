<?php
namespace App\Controller;

class AdminListController extends BaseController {

    public function show (){

        $admins = ['Sivert', 'Erlend', 'Aaryan'];


        return $this->render('admin/admin_list.html.twig', [
            'AdminList' => $admins
        ]);
    }

};
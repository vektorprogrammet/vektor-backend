<?php
namespace App\Controller;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class AdminListController extends BaseController {

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function show (){

        //$admins = ['Sivert', 'Erlend', 'Aaryan'];

       $admins = $this->doctrine->getRepository(User::class)->findAdmins();



        return $this->render('admin/admin_list.html.twig', [
            'AdminList' => $admins
        ]);
    }

};
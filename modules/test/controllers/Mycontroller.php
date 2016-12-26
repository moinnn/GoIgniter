<?php
namespace Modules\Test\Controllers;

class Mycontroller extends \CI_Controller{

    function index(){
        $model = new \Modules\Test\Models\MyModel();
        helper('test/date');
        $data = array(
            'articles' => $model->get_data(),
            'date'     => get_date()
        );
        view('test/MyView', $data);
    }

    function harambe($act){
        echo 'All hail harambe !!!'.PHP_EOL;
        echo 'Harambe is '.$act;
        if(isset($_GET['food']))
        {
            echo PHP_EOL . 'Give ' . $_GET['food'] . ' to harambe';
        }
    }

}

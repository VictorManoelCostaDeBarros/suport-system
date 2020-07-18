<?php 
    namespace controllers;
    
    class adminController
    {
        public function index() {
            \views\mainView::render('admin');
        }
    }
    
?>
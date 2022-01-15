<?php

class qTestController extends qControllerAction {
    
    public function action() {
        
        $action = $this->getArg(0, 'index');
        $batch = qBatch::init('qTestBatch', 'Przetwarzanie na śniadanie');
    }



}

